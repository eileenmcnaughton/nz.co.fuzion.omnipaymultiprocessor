<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

use Omnipay\Omnipay;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use Omnipay\Common\Http\Client;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpPlugClient;


/**
 * Class CRM_Core_Payment_OmnipayMultiProcessor.
 */
class CRM_Core_Payment_OmnipayMultiProcessor extends CRM_Core_Payment_PaymentExtended implements serializable {

  /**
   * For code clarity declare is_test as a boolean.
   *
   * @var bool
   */
  protected $_is_test = FALSE;

  /**
   * names of fields in payment processor table that relate to configuration of the processor instance
   *
   * @var array
   */
  protected $_configurationFields = [
    'user_name',
    'password',
    'signature',
    'subject',
  ];

  /**
   * Serialize, first removing gateway
   *
   * https://www.php.net/manual/en/class.serializable.php
   *
   * @return string
   */
  public function serialize() {
    $this->cleanupClassForSerialization(TRUE);
    return serialize(get_object_vars($this));
  }

  /**
   * Unserialize
   *
   * https://www.php.net/manual/en/class.serializable.php
   *
   * @param string $data
   */
  public function unserialize($data) {
    $values = unserialize($data);
    foreach ($values as $key => $value) {
      $this->$key = $value;
    }
  }

  /**
   * Omnipay gateway.
   *
   * @var AbstractGateway
   */
  protected $gateway;

  /**
   * Quickform key.
   *
   * This value references the user session if being used within a session.
   *
   * @var string
   */
  protected $qfKey;

  /**
   * @return string
   */
  public function getQfKey() {
    return $this->qfKey;
  }

  /**
   * @param string $qfKey
   */
  public function setQfKey($qfKey) {
    $this->qfKey = $qfKey;
  }

  /**
   * Process payment with external gateway.
   *
   * @param array $params assoc array of input parameters for this transaction
   *
   * @param string $component
   *
   * @return array
   *   The result in an nice formatted array (or an error object)
   * @throws CRM_Core_Exception
   */
  public function doPayment(&$params, $component = 'contribute') {
    // If we have a $0 amount, skip call to processor and set payment_status to Completed.
    if ($params['amount'] == 0) {
      return [
        'payment_status_id' => array_search('Completed', CRM_Contribute_BAO_Contribution::buildOptions('contribution_status_id', 'validate')),
      ];
    }
    $params['component'] = strtolower($component);
    $this->initialize($params);
    $this->saveBillingAddressIfRequired($params);

    try {
      if (!empty($params['token'])) {
        $response = $this->doTokenPayment($params);
      }
      elseif (!empty($params['is_recur'])) {
        $response = $this->gateway->createCard($this->getCreditCardOptions(array_merge($params, ['action' => 'Purchase']), $this->_component))->send();
      }
      else {
        $response = $this->gateway->purchase($this->getCreditCardOptions($params))
          ->send();
      }
      if ($response->isSuccessful()) {
        if (method_exists($response, 'getCardReference') && $response->getCardReference()) {
          $params['token'] = $response->getCardReference();
        }
        // mark order as complete
        if (!empty($params['is_recur'])) {
          $paymentToken = civicrm_api3('PaymentToken', 'create', [
            'contact_id' => $params['contactID'],
            'token' => $params['token'],
            'payment_processor_id' => $this->_paymentProcessor['id'],
            'created_id' => CRM_Core_Session::getLoggedInContactID(),
            'email' => $params['email'],
            'billing_first_name' => CRM_Utils_Array::value('billing_first_name', $params),
            'billing_middle_name' => CRM_Utils_Array::value('billing_middle_name', $params),
            'billing_last_name' => CRM_Utils_Array::value('billing_last_name', $params),
            'expiry_date' => $this->getCreditCardExpiry($params),
            'masked_account_number' => $this->getMaskedCreditCardNumber($params),
            'ip_address' => CRM_Utils_System::ipAddress(),
          ]);
          civicrm_api3('ContributionRecur', 'create', ['id' => $params['contributionRecurID'], 'payment_token_id' => $paymentToken['id']]);
        }
        $params['trxn_id'] = $response->getTransactionReference();
        $params['payment_status_id'] = 1;
        // @todo fetch masked card, card type, card expiry from params. Eway def provides these.
        //gross_amount ? fee_amount?
        return $params;
      }
      elseif ($response->isRedirect()) {
        $isTransparentRedirect = ($response->isTransparentRedirect() || !empty($this->gateway->transparentRedirect));
        $this->cleanupClassForSerialization(TRUE);
        CRM_Core_Session::storeSessionObjects(FALSE);
        if ($response->isTransparentRedirect()) {
          $this->storeTransparentRedirectFormData($params['qfKey'], $response->getRedirectData() + [
              'payment_processor_id' => $this->_paymentProcessor['id'],
              'post_submit_url' => $response->getRedirectURL(),
              'contact_id' => $params['contactID'],
            ]);
          $url = CRM_Utils_System::url('civicrm/payment/details', ['key' => $params['qfKey']]);
          $this->log('success_redirect', ['url' => $url]);
          CRM_Utils_System::redirect($url);
        }
        $response->redirect();
      }
      else {
        return $this->handleError('alert', 'failed processor transaction ' . $this->_paymentProcessor['payment_processor_type'], [$response->getCode() => $response->getMessage()]);
      }
    }
    catch (\Exception $e) {
      // internal error, log exception and display a generic message to the customer
      //@todo - looks like invalid credit card numbers are winding up here too - we could handle separately by capturing that exception type - what is good fraud practice?
      return $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], [$e->getCode() => $e->getMessage()], $e->getCode(), 'Sorry, there was an error processing your payment. Please try again later.');
    }
  }

  /**
   * Initialize class variables.
   *
   * @param array $params
   */
  protected function initialize(&$params) {
    $this->_component = $params['component'];
    $this->setQfKey(CRM_Utils_Array::value('qfKey', $params));
    $this->ensurePaymentProcessorTypeIsSet();
    $this->createGatewayObject();
    $this->setProcessorFields();
    $this->setContributionReference(CRM_Utils_Array::value('contributionID', $params));
    $this->storeReturnUrls(CRM_Utils_Array::value('participantID', $params), CRM_Utils_Array::value('eventID', $params));
  }

  /**
   * Get data that needs to be passed through to the processor.
   *
   * @return array
   */
  protected function getProcessorPassThroughFields() {
    $passThroughFields = $this->getProcessorTypeMetadata('pass_through_fields');
    return $passThroughFields ? $passThroughFields : [];
  }

  /**
   * Get core CiviCRM payment fields.
   *
   * @return array
   * @throws \CRM_Core_Exception
   */
  private function getCorePaymentFields() {
    $creditCardType = ['' => E::ts('- select -')] + CRM_Contribute_PseudoConstant::creditCard();
    return [
      'credit_card_number' => [
        'htmlType' => 'text',
        'name' => 'credit_card_number',
        'title' => ts('Card Number'),
        'cc_field' => TRUE,
        'attributes' => [
          'size' => 20,
          'maxlength' => 20,
          'autocomplete' => 'off',
        ],
        'is_required' => TRUE,
      ],
      'cvv2' => [
        'htmlType' => 'text',
        'name' => 'cvv2',
        'title' => ts('Security Code'),
        'cc_field' => TRUE,
        'attributes' => [
          'size' => 5,
          'maxlength' => 5,
          'autocomplete' => 'off',
        ],
        'is_required' => TRUE,
      ],
      'credit_card_exp_date' => [
        'htmlType' => 'date',
        'name' => 'credit_card_exp_date',
        'title' => ts('Expiration Date'),
        'cc_field' => TRUE,
        'attributes' => CRM_Core_SelectValues::date('creditCard'),
        'is_required' => TRUE,
        'month_field' => 'credit_card_exp_date_M',
        'year_field' => 'credit_card_exp_date_Y',
      ],

      'credit_card_type' => [
        'htmlType' => 'select',
        'name' => 'credit_card_type',
        'title' => ts('Card Type'),
        'cc_field' => TRUE,
        'attributes' => $creditCardType,
        'is_required' => FALSE,
      ],
      'card_name' => [
        'htmlType' => 'text',
        'name' => 'card_name',
        'title' => ts('Card Name'),
        'cc_field' => FALSE,
        'is_required' => TRUE,
        'contact_api' => 'display_name',
      ],
    ];
  }

  /**
   * Paypal express replaces the submit button with it's own.
   *
   * @return bool
   *   Should the form button by suppressed?
   * @throws \Civi\Payment\Exception\PaymentProcessorException
   */
  public function isSuppressSubmitButtons() {
    return $this->getProcessorTypeMetadata('suppress_submit_button');
  }

  /**
   * Opportunity for the payment processor to override the entire form build.
   *
   * @param CRM_Core_Form $form
   *
   * @return bool
   *   Should form building stop at this point?
   * @throws \Civi\Payment\Exception\PaymentProcessorException
   */
  public function buildForm(&$form) {
    $regions = $this->getProcessorTypeMetadata('regions');
    $jsVariables = [
      'paymentProcessorId' => $this->_paymentProcessor['id'],
      'currency' => $form->getCurrency(),
      'is_test' => $this->_is_test,
      'title' => $form->getTitle(),
    ];
    $clientSideCredentials = $this->getProcessorTypeMetadata('client_side_credentials');
    if ($clientSideCredentials) {
      foreach ($clientSideCredentials as $key => $clientSideKey) {
        $jsVariables[$clientSideKey] = $this->_paymentProcessor[$key];
      }
    }
    CRM_Core_Resources::singleton()->addVars('omnipay', $jsVariables);
    if (is_array($regions)) {
      foreach ($regions as $region => $additions) {
        foreach ($additions as $addition) {
          CRM_Core_Region::instance($region)->add(
            $addition
          );
        }
      }
    }
    return FALSE;
  }

  /**
   * Set fields on payment processor based on the labels.
   *
   * This is based on the payment_processor_type table & the values in the payment_processor table.
   *
   * @throws CRM_Core_Exception
   */
  public function setProcessorFields() {
    $fields = $this->getProcessorFields();
    try {
      foreach ($fields as $name => $value) {
        $fn = "set{$name}";
        if (method_exists($this->gateway, $fn)) {
          $this->gateway->$fn($value);
        }
      }
      if (\Civi::settings()->get('omnipay_test_mode')) {
        $this->_is_test = TRUE;
      }
      if (in_array('testMode', array_keys($this->gateway->getDefaultParameters()))) {
        if (method_exists($this->gateway, 'setDeveloperMode')) {
          $this->gateway->setDeveloperMode($this->_is_test);
        }
        else {
          $this->gateway->setTestMode($this->_is_test);
        }
      }
    }
    catch (Exception $e) {
      throw new CRM_Core_Exception('Processor is incorrectly configured');
    }
  }

  /**
   * Get array of payment processor configuration fields keyed by the relevant payment processor properties.
   *
   * For example the field might be displayed as 'Secret Key' - the payment processor property is secretKey
   * We will give 'ID special treatment as it would be pretty ugly to present the end user with a label saying 'Id'
   *
   * @return array
   *   Payment processor configuration fields
   *
   * @throws CiviCRM_API3_Exception
   */
  function getProcessorFields() {
    $labelFields = $result = [];
    foreach ($this->_configurationFields as $configField) {
      if (!empty($this->_paymentProcessor[$configField])) {
        $labelFields[$configField] = "{$configField}_label";
      }
    }
    $processorFields = civicrm_api3('payment_processor_type', 'getsingle', [
        'id' => $this->_paymentProcessor['payment_processor_type_id'],
        'return' => $labelFields,
      ]
    );

    foreach ($labelFields as $field => $label) {
      $result[$this->camelFieldName($processorFields[$label])] = $this->_paymentProcessor[$field];
    }
    return $result;
  }

  /**
   * Get preapproval details that have been set.
   *
   * @param array $detail
   *
   * @return array mixed
   */
  function getPreApprovalDetails($detail) {
    return $detail;
  }

  /**
   * Core specifically won't save billing address if you use notify mode so we make up for that here.
   *
   * For example a transparent Redirect (POST credit card form off-site) processor has some features of each.
   *
   * Rather than try to categorise the processor we say 'if a contribution exists and it does not have a billing address but we have billing
   * fields in our form we will create a billing address for the payment
   *
   * @param array $params
   */
  private function saveBillingAddressIfRequired($params) {
    if (!empty($params['contributionID']) && $this->hasBillingAddressFields($params)) {
      $contribution = civicrm_api3('contribution', 'getsingle', ['id' => $params['contributionID'], 'return' => 'address_id, contribution_status_id']);
      if (empty($contribution['address_id'])) {
        civicrm_api3('contribution', 'create', [
          'id' => $params['contributionID'],
          // required due to CRM-15105
          'contribution_status_id' => $contribution['contribution_status_id'],
          'address_id' => CRM_Contribute_BAO_Contribution::createAddress($params, CRM_Core_BAO_LocationType::getBilling()),
        ]);
      }
    }
  }

  /**
   * Check for billing address fields.
   *
   * Are there any billing address fields in the params array (not including billing-first-name - only true address fields)
   *
   * @param array $params
   *
   * @return bool
   */
  private function hasBillingAddressFields($params) {
    $billingFields = array_intersect_key($params, array_flip($this->getBillingAddressFields()));
    return !empty($billingFields);
  }

  /**
   * Get fieldname in format used in gateway functions $this->gateway->setSecretKey.
   *
   * We remove spaces & camel it
   *
   * @param string $fieldName
   *
   * @return string
   */
  private function camelFieldName($fieldName) {
    return str_replace(' ', '', ucwords(strtolower($fieldName)));
  }

  /**
   * Generate card object from CiviCRM params.
   *
   * At this stage we are not yet mapping
   * - startMonth
   * - startYear
   * - issueNumber
   * - frequency_interval,
   * - frequency_day,
   * - frequency_installments
   * - shipping fields
   *
   * @param array $params
   *
   * @return array
   */
  private function getCreditCardObjectParams($params) {
    $billingID = $locationTypes = CRM_Core_BAO_LocationType::getBilling();
    $cardFields = [];
    $basicMappings = [
      'firstName' => 'billing_first_name',
      'lastName' => 'billing_last_name',
      'email' => 'email',
      'billingAddress1' => 'billing_street_address-' . $billingID,
      'billingAddress2' => 'supplemental_address-' . $billingID,
      'billingCity' => 'billing_city-' . $billingID,
      'billingPostcode' => 'billing_postal_code-' . $billingID,
      'billingState' => 'billing_state_province-' . $billingID,
      'billingCountry' => 'billing_country-' . $billingID,
      // we don't specifically anticipate phone to come through - adding this & company as 'best guess'
      'billingPhone' => 'phone',
      'company' => 'organization_name',
      'type' => 'credit_card_type',
    ];

    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
    }

    // billingCountry and billingState are numeric and in "_id" fields when submitted via a contribution confirm page
    if (empty($cardFields['billingCountry']) && isset($params['billing_country_id-' . $billingID])) {
      $cardFields['billingCountry'] = $params['billing_country_id-' . $billingID];
    }
    if (is_numeric($cardFields['billingCountry'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::countryIsoCode($cardFields['billingCountry']);
    }
    if (empty($cardFields['billingState']) && isset($params['billing_state_province_id-' . $billingID])) {
      $cardFields['billingState'] = $params['billing_state_province_id-' . $billingID];
    }
    if (is_numeric($cardFields['billingState'])) {
      $cardFields['billingState'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation($cardFields['billingState']);
    }

    if (empty($cardFields['email'])) {
      if (!empty($params['email-' . $billingID])) {
        $cardFields['email'] = $params['email-' . $billingID];
      }
      elseif (!empty($params['email-Primary'])) {
        $cardFields['email'] = $params['email-Primary'];
      }
      else {
        foreach ($params as $fieldName => $value) {
          if (substr($fieldName, 0, 5) == 'email') {
            $cardFields['email'] = $value;
          }
        }
      }
    }
    return $cardFields;
  }

  /**
   * Get sensitive credit card fields.
   *
   * @param $params
   *
   * @return mixed
   */
  private function getSensitiveCreditCardObjectOptions($params) {
    $basicMappings = [
      'cvv' => 'cvv2',
      'number' => 'credit_card_number',
    ];
    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
    }
    if (!empty($params['credit_card_exp_date'])) {
      $cardFields['expiryMonth'] = $params['credit_card_exp_date']['M'];
      $cardFields['expiryYear'] = $params['credit_card_exp_date']['Y'];
    }
    return $cardFields;
  }

  /**
   * Get options for credit card.
   *
   * @param array $params
   *
   * @return array
   * @throws \CRM_Core_Exception
   */
  protected function getCreditCardOptions($params) {
    $creditCardOptions = [
      'amount' => $this->getAmount($params),
      'currency' => $this->getCurrency($params),
      'description' => $this->getPaymentDescription($params),
      'transactionId' => $this->formatted_transaction_id,
      'clientIp' => CRM_Utils_System::ipAddress(),
      'returnUrl' => $this->getNotifyUrl(TRUE),
      'cancelUrl' => $this->getCancelUrl($this->getQfKey(), CRM_Utils_Array::value('participantID', $params)),
      'notifyUrl' => $this->getNotifyUrl(),
      'card' => $this->getCreditCardObjectParams($params),
      'cardReference' => CRM_Utils_Array::value('token', $params),
      'transactionReference' => CRM_Utils_Array::value('token', $params),
      'cardTransactionType' => CRM_Utils_Array::value('cardTransactionType', $params),
    ];
    if (!empty($params['action'])) {
      $creditCardOptions['action'] = 'Purchase';
    }

    // Add any passthrough fields in. This allows us to define fields in metadata
    // and have them reach Omnipay with only a small amount of twisting ourselves in knots.
    foreach ($this->getPaymentFormFieldsMetadata() as $field) {
      if (!empty($field['is_pass_through']) && isset($params[$field['name']])) {
        $creditCardOptions[$field['name']] = $params[$field['name']];
      }
    }
    $creditCardOptions = array_merge($creditCardOptions, $this->getProcessorPassThroughFields());

    CRM_Utils_Hook::alterPaymentProcessorParams($this, $params, $creditCardOptions);
    $creditCardOptions['card'] = array_merge($creditCardOptions['card'], $this->getSensitiveCreditCardObjectOptions($params));
    return $creditCardOptions;
  }

  /**
   * This function checks to see if we have the right config values.
   *
   * @return string
   *   The error message if any
   */
  public function checkConfig() {
    //@todo check gateway against $this->gateway->getDefaultParameters();
  }

  /**
   * Get array of fields that should be displayed on the payment form.
   *
   * @return array
   *
   * @throws CiviCRM_API3_Exception
   */
  public function getPaymentFormFields() {
    $fields = $this->getProcessorTypeMetadata('payment_fields');
    if ($fields !== FALSE) {
      return $fields;
    }
    if ($this->_paymentProcessor['billing_mode'] == 4 || $this->isTransparentRedirect()) {
      return [];
    }
    return $this->_paymentProcessor['payment_type'] == 1 ? $this->getCreditCardFormFields() : $this->getDirectDebitFormFields();
  }


  /**
   * Return an array of all the details about the fields potentially required for payment fields.
   *
   * Only those determined by getPaymentFormFields will actually be assigned to the form
   *
   * @return array
   *   field metadata
   */
  public function getPaymentFormFieldsMetadata() {
    $fields = parent::getPaymentFormFieldsMetadata();
    $fields['card_name'] = [
      'htmlType' => 'text',
      'name' => 'card_name',
      'title' => E::ts('Card Name'),
      'cc_field' => FALSE,
      'is_required' => TRUE,
      'contact_api' => 'display_name',
    ];
    $additionalMetadata = $this->getProcessorTypeMetadata('payment_fields_metadata');
    if ($additionalMetadata) {
      $fields = array_merge($fields, $additionalMetadata);
    }
    if ($this->isClientSideEncrypted()) {
      unset($fields['cvv2']['rules']);
    }
    return $fields;
  }

  /**
   * Get billing fields required for this block.
   *
   * @return array
   * @todo move this metadata requirement onto the class - or the mgd files
   */
  public function getBillingBlockFields() {
    $billingID = $locationTypes = CRM_Core_BAO_LocationType::getBilling();
    //for now we will cheat & just use the really blunt characteristics option - ie.
    // ie billing mode 1 or payment type 3 get billing fields.
    // we really want this to be metadata of the payment processors
    if ($this->_paymentProcessor['billing_mode'] != 1 && $this->_paymentProcessor['payment_type'] != 3) {
      return [];
    }
    return [
      'first_name' => 'billing_first_name',
      //'middle_name' => 'billing_middle_name',
      'last_name' => 'billing_last_name',
      'street_address' => "billing_street_address-{$billingID}",
      'city' => "billing_city-{$billingID}",
      'country' => "billing_country_id-{$billingID}",
      'state_province' => "billing_state_province_id-{$billingID}",
      'postal_code' => "billing_postal_code-{$billingID}",
    ];
  }

  /**
   * Get the fields to display for transparent direct method.
   *
   * This is the method where we post first to CiviCRM & then do a form POST to the off site processor
   * with extra fields not included in the CiviCRM form.
   *
   * It is conceivable that we could POST to the processor first but that possibility is not yet applicable to
   * the processors we have
   *
   * The metadata for the processor declares what fields to display.
   */
  public function getTransparentDirectDisplayFields() {
    $fields = $this->getProcessorTypeMetadata('transparent_redirect');
    if (isset($fields['fields'])) {
      $fields = $fields['fields'];
      $coreFields = $this->getPaymentFormFieldsMetadata();
      foreach ($fields as $fieldName => $field) {
        if (isset($coreFields[$field['core_field_name']])) {
          $fields[$fieldName] = array_merge($coreFields[$field['core_field_name']], $field);
        }
      }
      return $fields;
    }
    return [];
  }

  /**
   * Get core CiviCRM address fields.
   *
   * @param int $billingLocationID
   *
   * @return array
   */
  public function getBillingAddressFields($billingLocationID = NULL) {
    $fields = $this->getProcessorTypeMetadata('fields');
    if (isset ($fields['billing_fields'])) {
      return $fields['billing_fields'];
    }
    if (!$this->isTransparentRedirect()) {
      return parent::getBillingAddressFields($billingLocationID);
    }
    $fields = $this->getProcessorTypeMetadata('transparent_redirect');
    if (isset ($fields['billing_fields'])) {
      return $fields['billing_fields'];
    }
    $billingFields = [
      'first_name' => 'billing_first_name',
      'middle_name' => 'billing_middle_name',
      'last_name' => 'billing_last_name',
    ];
    foreach ([
               'street_address',
               'city',
               'state_province_id',
               'postal_code',
               'country_id',
             ] as $addressField) {
      $billingFields[$addressField] = 'billing_' . $addressField . '-' . CRM_Core_BAO_LocationType::getBilling();
    }
    return $billingFields;
  }

  /**
   * Handle response from processor.
   *
   * We simply get the params from the REQUEST and pass them to a static function that
   * can also be called / tested outside the normal process
   */
  public function handlePaymentNotification() {
    $params = array_merge($_GET, $_REQUEST);
    if (empty($params['payment_processor_id'])) {
      // CRM-16422 we need to be prepared for the payment processor id to be in the url instead.
      $q = explode('/', CRM_Utils_Array::value('q', $params, ''));
      $lastParam = array_pop($q);
      if (is_numeric($lastParam)) {
        $params['processor_id'] = $lastParam;
      }
    }

    $paymentProcessorID = $params['processor_id'];
    $this->_paymentProcessor = civicrm_api3('payment_processor', 'getsingle', ['id' => $paymentProcessorID]);
    $this->_paymentProcessor['name'] = civicrm_api3('payment_processor_type', 'getvalue', ['id' => $this->_paymentProcessor['payment_processor_type_id'], 'return' => 'name']);
    $this->processPaymentNotification($params);
  }

  /**
   * Update Transaction based on outcome of the API.
   *
   * @param array $params
   *
   * @throws CRM_Core_Exception
   * @throws CiviCRM_API3_Exception
   */
  public function processPaymentNotification($params) {
    $this->createGateway($params['processor_id']);
    $originalRequest = $_REQUEST;
    $_REQUEST = $params;
    $response = NULL;
    try {
      if ($this->gateway->supportsAcceptNotification()) {
        $response = $this->gateway->acceptNotification($params)->send();
      }
      else {
        $response = $this->gateway->completePurchase($params)->send();
      }
      if ($response->getTransactionId()) {
        $this->setContributionReference($response->getTransactionId(), 'strip');
      }
    }
    catch (\Omnipay\Common\Exception\InvalidRequestException $e) {
      $q = explode('/', CRM_Utils_Array::value(CRM_Core_Config::singleton()->userFrameworkURLVar, $_GET, ''));
      array_pop($q);
      $this->setContributionReference(array_pop($q));
      if (!civicrm_api3('Contribution', 'getcount', [
        'id' => $this->transaction_id,
        'contribution_status_id' => ['IN' => ['Completed', 'Pending']],
      ])) {
        $this->redirectOrExit('fail', $response);
      }
      $this->redirectOrExit('success', $response);
    }
    // Unset gateway once we are done with it per https://github.com/eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor/issues/55
    $this->gateway = NULL;

    if ($response->isSuccessful()) {
      try {
        //cope with CRM14950 not being implemented
        $contribution = civicrm_api3('contribution', 'getsingle', [
          'id' => $this->transaction_id,
          //'return' => 'contribution_status_id, contribution_recur_id, contact_id, contribution_contact_id',
        ]);

        if ($this->getLock() && CRM_Core_PseudoConstant::getName('CRM_Contribute_BAO_Contribution', 'contribution_status_id', $contribution['contribution_status_id']) !== 'Completed') {
          civicrm_api3('contribution', 'completetransaction', [
            'id' => $this->transaction_id,
            'trxn_id' => $response->getTransactionReference(),
            'payment_processor_id' => $params['processor_id'],
          ]);
        }
        if (!empty($contribution['contribution_recur_id']) && ($tokenReference = $response->getCardReference()) != FALSE) {
          $this->storePaymentToken($params, $contribution, $tokenReference);
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        if (stripos($e->getMessage(), 'Contribution already completed') === FALSE) {
          $this->handleError('error', 'ipn_completion failed', $this->transaction_id . $e->getMessage(), 9000, 'An error may have occurred. Please check your receipt is correct');
        }
      }
      $_REQUEST = $originalRequest;
      $this->redirectOrExit('success', $response);
    }
    elseif ($this->transaction_id) {
      // Mark the contribution as failed (only allowed if status=Pending).
      // We get multiple requests from some processors (eg. Sagepay) where the contribution has already been marked as "Cancelled".
      try {
        $contribution = civicrm_api3('contribution', 'getsingle', [
          'id' => $this->transaction_id,
          'return' => 'contribution_status_id',
        ]);

        $contributionStatusName = CRM_Core_PseudoConstant::getName('CRM_Contribute_BAO_Contribution', 'contribution_status_id', $contribution['contribution_status_id']);
        if ($contributionStatusName === 'Pending') {
          civicrm_api3('contribution', 'create', ['id' => $this->transaction_id, 'contribution_status_id' => 'Failed']);
        }
      }
      catch (Exception $e) {
        Civi::log()->error('CRM_Core_Payment_OmnipayMultiProcessor::processPaymentNotification: ' . $e->getMessage());
      }
    }

    $_REQUEST = $originalRequest;
    $this->redirectOrExit('fail', $response);
  }

  /**
   * Get a lock so we don't process browser return & ipn return at the same time.
   *
   * Paralell processing notably results in 2 receipts.
   *
   * Currently mysql 5.7.5+ will process a cross-session lock. If we can't do that
   * then we should be tardy on the processing of the ipn response.
   *
   * @return bool
   */
  protected function getLock() {
    $mysqlVersion = CRM_Core_DAO::singleValueQuery('SELECT VERSION()');
    if (stripos($mysqlVersion, 'mariadb') === FALSE
      && version_compare($mysqlVersion, '5.7.5', '>=')
    ) {
      $lock = Civi::lockManager()->acquire('data.contribute.contribution.' . $this->transaction_id);
      return $lock->isAcquired();
    }
    if (empty(CRM_Core_Session::singleton()->getLoggedInContactID())) {
      $delay = $this->getProcessorTypeMetadata('ipn_processing_delay');
      if (!is_numeric($delay)) {
        $delay = 45;
      }
      // So far the best way of telling the difference is the session.
      sleep($delay);
    }
    return TRUE;
  }

  public function queryPaymentPlans($params) {
    $this->createGateway($this->_paymentProcessor['id']);
    $response = $this->gateway->paymentPlansQuery($params)->send();
    return $response->getPlanData();
  }

  /**
   * @param $params
   *
   * @return mixed
   *
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function query($params) {
    $this->createGateway($this->_paymentProcessor['id']);
    $response = $this->gateway->query($params)->send();
    if ($response->isSuccessful()) {
      return $response->getData();
    }
    throw new CRM_Core_Exception('Failed to retrieve data');
  }

  /**
   * Static wrapper for IPN / Payment response handling - this allows us to re-call from the api.
   *
   * @param array $params
   *
   * @return bool
   * @throws CiviCRM_API3_Exception
   * @throws \CRM_Core_Exception
   */
  public static function processPaymentResponse($params) {
    $processor = civicrm_api3('payment_processor', 'getsingle', ['id' => $params['processor_id']]);
    $responder = new CRM_Core_Payment_OmnipayMultiProcessor('live', $processor);
    $responder->processPaymentNotification($params);
    return TRUE;
  }

  /**
   * Redirect browser or exit gracefully.
   *
   * We don't know if we are dealing with a user browser or an IPN. If a browser
   * they should have the end point stored in their session so we redirect to it.
   *
   * Otherwise we present a blank screen.
   *
   * Note - which is worse - risk a blank screen to users or redirect IPNs to
   * homepage?
   *
   * @param string $outcome
   *  - success
   *  - fail
   * @param null|object $response
   */
  protected function redirectOrExit($outcome, $response = NULL) {
    switch ($outcome) {
      case 'fail':
        $userMsg = E::ts('Your payment was not successful. Please try again');
        CRM_Core_Session::setStatus($userMsg);
        $redirectUrl = $this->getStoredUrl('fail');
        if (!$redirectUrl && method_exists($response, 'invalid')) {
          $response->invalid($redirectUrl, $userMsg);
        }
        break;

      case 'error':
        $userMsg = E::ts('The transaction was not processed. The message from the bank was : %1. Please try again', [1 => $response->getMessage()]);
        if ($response && method_exists($response, 'getInvalidFields') && ($invalidFields = $response->getInvalidFields()) != []) {
          $userMsg = E::ts('Invalid data entered in fields ' . implode(', ', $invalidFields));
        }
        CRM_Core_Session::setStatus($userMsg);
        $redirectUrl = $this->getStoredUrl('fail');
        if ($response && method_exists($response, 'error')) {
          $response->error($redirectUrl, $userMsg);
        }
        try {
          $this->handleError('error', $this->transaction_id . ' ' . $response->getMessage(), ['processor_error', $response->getMessage()], 9002, $userMsg);
        }
        catch (\Civi\Payment\Exception\PaymentProcessorException $e) {

        }
        break;
      case 'success':
        $userMsg = NULL;
        $redirectUrl = $this->getStoredUrl('success');
        if (!$redirectUrl && method_exists($response, 'confirm')) {
          $output = $response->confirm($redirectUrl, $userMsg);
          if ($output) {
            echo $output;
          }
        }
        break;
    }

    if ($redirectUrl != FALSE) {
      CRM_Utils_System::redirect($redirectUrl);
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * Store a payment token.
   *
   * We don't want fails here to trigger a rollback to we set is_transactional to false.
   *
   * Note that we are 'always' setting the next sched contribution date here - but that
   * might be more conditional if we support Authorize.net style delay in future.
   *
   * @param array $params
   * @param array $contribution
   * @param string $tokenReference
   *
   * @throws \CiviCRM_API3_Exception
   */
  protected function storePaymentToken($params, $contribution, $tokenReference) {
    $contributionRecurID = $contribution['contribution_recur_id'];
    $token = civicrm_api3('payment_token', 'create', [
      'contact_id' => $contribution['contact_id'],
      'payment_processor_id' => $params['processor_id'],
      'token' => $tokenReference,
      'is_transactional' => FALSE,
      'created_id' => (CRM_Core_Session::singleton()->getLoggedInContactID() ?: $contribution['contact_id']),
    ]);
    $contributionRecur = civicrm_api3('ContributionRecur', 'getsingle', ['id' => $contributionRecurID]);
    civicrm_api3('contribution_recur', 'create', [
      'id' => $contributionRecurID,
      'payment_token_id' => $token['id'],
      'is_transactional' => FALSE,
      'next_sched_contribution_date' => CRM_Utils_Date::isoToMysql(
        date('Y-m-d 00:00:00', strtotime('+' . $contributionRecur['frequency_interval'] . ' ' . $contributionRecur['frequency_unit']))
      ),
    ]);
  }

  /**
   * Ensure payment processor type is set.
   *
   * It's kind of 'hacked on' to the payment_processor params normally but not when called form
   * the pay api.
   *
   * @throws \CiviCRM_API3_Exception
   */
  protected function ensurePaymentProcessorTypeIsSet() {
    if (!isset($this->_paymentProcessor['payment_processor_type'])) {
      $this->_paymentProcessor['payment_processor_type'] = civicrm_api3('PaymentProcessorType', 'getvalue', [
        'id' => $this->_paymentProcessor['payment_processor_type_id'],
        'return' => 'name',
      ]);
    }
  }

  /**
   * Is this a transparent redirect processor.
   *
   * Transparent redirect refers to a processor which presents a form to be POSTed off-site.
   *
   * Generally (and in the case of Cybersource specifically) they collect billing details first.
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  private function isTransparentRedirect() {
    $paymentType = civicrm_api3('option_value', 'getsingle', [
      'value' => $this->_paymentProcessor['payment_type'],
      'option_group_id' => 'payment_type',
    ]);
    if ($paymentType['name'] == 'credit_card_off_site_post') {
      return TRUE;
    }
  }

  /**
   * Should the first payment date be configurable when setting up back office recurring payments.
   * In the case of Authorize.net this is an option
   *
   * @return bool
   */
  protected function supportsFutureRecurStartDate() {
    return $this->_paymentProcessor['is_recur'];
  }

  /**
   * Is an authorize-capture flow supported.
   *
   * @return bool
   */
  protected function supportsPreApproval() {
    return $this->getProcessorTypeMetadata('supports_preapproval');
  }

  /**
   * Does this processor support cancelling recurring contributions through code.
   *
   * If the processor returns true it must be possible to take action from within CiviCRM
   * that will result in no further payments being processed.
   *
   * @return bool
   */
  protected function supportsCancelRecurring() {
    return TRUE;
  }

  public function cancelSubscription() {
    // We take no action here - the key thing is that the contribution_recur record is updated.
  }

  /**
   * Does this processor support changing the amount for recurring contributions through code.
   *
   * If the processor returns true then it must be possible to update the amount from within CiviCRM
   * that will be updated at the payment processor.
   *
   * @return bool
   */
  public function supportsEditRecurringContribution() {
    return TRUE;
  }

  /**
   * Function to action pre-approval if supported
   *
   * @param array $params
   *   Parameters from the form
   *
   * This function returns an array which should contain
   *   - pre_approval_parameters (this will be stored on the calling form & available later)
   *   - redirect_url (if set the browser will be redirected to this.
   *
   * @return array
   */
  public function doPreApproval(&$params) {
    if (!empty($params['payment_token'])) {
      return ['pre_approval_parameters' => ['token' => $params['payment_token']]];
    }
    $this->initialize($params);
    $this->saveBillingAddressIfRequired($params);

    try {
      if (!empty($params['is_recur'])) {
        $response = $this->doPreApproveForRecurring($params);
      }
      else {
        // I looked at authorize rather than purchase but for both paypal it seems worse & for eway
        // some complaint about double email.
        $response = $this->gateway->purchase($this->getCreditCardOptions($params))
          ->send();
      }

      if ($response->isSuccessful()) {
        $params['trxn_id'] = $params['token'] = $response->getTransactionReference();
        $cardReference = $response->getTransactionReference();
        if (!empty($params['is_recur']) && $cardReference) {
          $params['token'] = $cardReference;
        }

        $creditCardPan = $this->getMaskedCreditCardNumber($params);
        foreach ($_SESSION as $key => $value) {
          if (isset($value['values'])) {
            foreach ($value['values'] as $pageName => $pageValues) {
              if (isset($pageValues['credit_card_number'])) {
                unset($_SESSION[$key]['values'][$pageName]['cvv2']);
                $_SESSION[$key]['values'][$pageName]['credit_card_number'] = $creditCardPan;
              }
            }
          }
        }
        $this->gateway = NULL;
        unset($params['credit_card_number']);
        unset($params['cvv2']);
        $this->log('success', $params);
        return [
          'pre_approval_parameters' => ['token' => $params['token']],
        ];
      }
      elseif ($response->isRedirect()) {

        if ($response->getTransactionReference()) {
          // For Paypal express with jsv4 we should just return the token.
          // This is kinda tricky - in that it's not denoted on that class anywhere
          // & as we integrate more we might need to refine this early return
          // to be metadata based or to have some jsv4 specific paypal class.
          return ['pre_approval_parameters' => ['token' => $response->getTransactionReference()]];
        }
        /*
         * This is what we expect to do but no current processors.
        $isTransparentRedirect = ($response->isTransparentRedirect() || !empty($this->gateway->transparentRedirect));
        // Unset $this->gateway before storing session to cache due to risk of
        // Serialization of 'Closure' is not allowed error - issue #17
        $this->gateway = NULL;
        CRM_Core_Session::storeSessionObjects(FALSE);
        if ($response->isTransparentRedirect()) {
          $this->storeTransparentRedirectFormData($params['qfKey'], $response->getRedirectData() + array(
              'payment_processor_id' => $this->_paymentProcessor['id'],
              'post_submit_url' => $response->getRedirectURL(),
              'contact_id' => $params['contactID'],
            ));
          CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/payment/details', array('key' => $params['qfKey'])));
        }
        $response->redirect();
        */
      }
      else {
        $this->purgeSensitiveDataFromSession();
        unset($params['credit_card_number']);
        unset($params['cvv2']);
        $contextKeys = ['id', 'name', 'payment_processor_type_id', 'payment_processor_type', 'is_test'];
        return $this->handleError('alert', 'failed processor transaction', array_intersect_key($this->_paymentProcessor, array_flip($contextKeys)), 9001, $response->getMessage());
      }
    }
    catch (\Exception $e) {
      $this->purgeSensitiveDataFromSession();
      // internal error, log exception and display a generic message to the customer
      $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], [$e->getCode() => $e->getMessage()], $e->getCode(), 'Sorry, there was an error processing your payment. Please try again later.');
    }
  }

  /**
   * Function to action after pre-approval if supported
   *
   * @param array $params
   *   Parameters from the form
   *
   * Action to do after pre-approval. e.g. PaypalRest returns from offsite &
   * hits the billing plan url to confirm.
   *
   * This is a fairly immature concept - PaypalRest requires that the token
   * be authorized by us after it is authorized by the user.
   *
   * The flow is
   *   - user clicks paypal button
   *   - our doPreapprove is called - we get a token from paypal
   *   - we return the token to the paypal js script
   *   - the paypal js script launches a pop up where the user enters their approval
   *   - on close we get call paypal to complete the payment
   *   - for recurring payments this requires 2 calls - one to complete the recurring authorisation
   *   and one to process a payment against it.
   *
   * This happens before the payment
   * is processed but after they have submitted the form to us.
   *
   * @throws \CRM_Core_Exception
   */
  public function doRecurPostApproval(&$params) {
    if (!$this->getProcessorTypeMetadata('post_recur_action') === 'completeCreateCard') {
      return;
    }

    $planResponse = $this->gateway->completeCreateCard(array_merge($this->getProcessorPassThroughFields(), [
      'transactionReference' => $params['token'],
    ]))->send();
    if (!$planResponse->isSuccessful()) {
      throw new CRM_Core_Exception($planResponse->getMessage());
    }
    $params['token'] = $planResponse->getCardReference();
  }

  /**
   * Get an array of the fields that can be edited on the recurring contribution.
   *
   * Some payment processors support editing the amount and other scheduling details of recurring payments, especially
   * those which use tokens. Others are fixed. This function allows the processor to return an array of the fields that
   * can be updated from the contribution recur edit screen.
   *
   * The fields are likely to be a subset of these
   *  - 'amount',
   *  - 'installments',
   *  - 'frequency_interval',
   *  - 'frequency_unit',
   *  - 'cycle_day',
   *  - 'next_sched_contribution_date',
   *  - 'end_date',
   * - 'failure_retry_date',
   *
   * The form does not restrict which fields from the contribution_recur table can be added (although if the html_type
   * metadata is not defined in the xml for the field it will cause an error.
   *
   * Open question - would it make sense to return membership_id in this - which is sometimes editable and is on that
   * form (UpdateSubscription).
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public function getEditableRecurringScheduleFields() {
    $possibles = ['amount'];
    $fields = civicrm_api3('ContributionRecur', 'getfields', ['action' => 'create']);
    // The html is only set in 4.7.11 +
    // The date fields look a bit funky at the moment so not adding all possible fields.
    if (!empty($fields['values']['next_sched_contribution_date']['html'])) {
      $possibles[] = 'next_sched_contribution_date';
      $possibles[] = 'installments';
      $possibles[] = 'frequency_interval';
      $possibles[] = 'frequency_unit';
    }
    return $possibles;
  }

  /**
   * @param int $id
   *
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  private function createGateway($id) {
    $paymentProcessorTypeId = civicrm_api3('payment_processor', 'getvalue', [
      'id' => $id,
      'return' => 'payment_processor_type_id',
    ]);
    $paymentProcessorTypeName = civicrm_api3('payment_processor_type', 'getvalue', [
      'id' => $paymentProcessorTypeId,
      'return' => 'name',
    ]);
    $this->_paymentProcessor['payment_processor_type'] = $paymentProcessorTypeName;
    $this->createGatewayObject();
    $this->setProcessorFields();
  }

  /**
   * Remove sensitive data from the session before it is stored.
   */
  protected function purgeSensitiveDataFromSession() {
    foreach ($_SESSION as &$key) {
      if (isset($key['values']) && is_array($key['values'])) {
        foreach ($key['values'] as &$values) {
          foreach ([
                     'credit_card_number',
                     'cvv2',
                     'credit_cate_type',
                   ] as $fieldName) {
            if (!empty($values[$fieldName])) {
              $values[$fieldName] = '';
            }
          }
          if (isset($values['credit_card_exp_date'])) {
            $values['credit_card_exp_date'] = ['M' => '', 'Y' => ''];
          }
        }
      }
    }
  }

  /**
   * @return array
   */
  protected function getEntitiesMetadata() {
    // Cache 'omnipay_entities_metadata' (refs #56).
    $entities = Civi::cache()->get('omnipay_entities_metadata');
    if (!$entities) {
      $entities = [];
      omnipaymultiprocessor_civicrm_managed($entities);
      Civi::cache()->set('omnipay_entities_metadata', $entities);
    }
    return $entities;
  }

  /**
   * Get metadata for a processor entity.
   *
   * @param string $parameter
   *
   * @return mixed
   */
  protected function getProcessorTypeMetadata($parameter) {
    $entities = $this->getEntitiesMetadata();
    foreach ($entities as $entity) {
      if ($entity['entity'] === 'payment_processor_type') {
        if (!isset($this->_paymentProcessor['payment_processor_type'])) {
          $this->_paymentProcessor['payment_processor_type'] = civicrm_api3('PaymentProcessorType', 'getvalue', ['id' => $this->_paymentProcessor['payment_processor_type_id'], 'return' => 'name']);
        }
        if (
          $entity['params']['name'] === $this->_paymentProcessor['payment_processor_type']
          && isset($entity['metadata'][$parameter])) {
          return $entity['metadata'][$parameter];
        }
      }
    }
    return FALSE;
  }

  /**
   * @param $params
   *
   * @return string
   */
  protected function getMaskedCreditCardNumber(&$params) {
    if (empty($params['credit_card_number'])) {
      return '';
    }
    $creditCardPan = '************' . substr($params['credit_card_number'], -4);
    return $creditCardPan;
  }

  /**
   * Instantiate $this->gateWay.
   */
  protected function createGatewayObject() {
    $parameters = NULL;
    if (\Civi::settings()->get('omnipay_developer_mode')) {

      // Create a history plugin and attach it to the client
      $this->history = [];
      $history = Middleware::history($this->history);
      $stack = HandlerStack::create();
      $stack->push($history);
      $this->guzzleClient = HttpPlugClient::createWithConfig(['handler' => $stack]);
      $this->client = new Client($this->guzzleClient);
      Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->client];
    }
    if (isset(Civi::$statics['Omnipay_Test_Config']['client'])) {
      $parameters = Civi::$statics['Omnipay_Test_Config']['client'];
    };
    $request = NULL;
    if (isset(Civi::$statics['Omnipay_Test_Config']['request'])) {
      $request = Civi::$statics['Omnipay_Test_Config']['request'];
    };
    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $this->_paymentProcessor['payment_processor_type']), $parameters, $request);
  }

  /**
   * Default payment instrument validation.
   *
   * Implement the usual Luhn algorithm via a static function in the CRM_Core_Payment_Form if it's a credit card
   * Not a static function, because I need to check for payment_type.
   *
   * @param array $values
   * @param array $errors
   */
  public function validatePaymentInstrument($values, &$errors) {
    if ($this->isClientSideEncrypted()) {
      // Perhaps later we can do some form of encryption.
    }
    else {
      parent::validatePaymentInstrument($values, $errors);
    }
  }

  /**
   * @return bool
   */
  protected function isClientSideEncrypted() {
    $isClientSideEncrypted = FALSE;
    $clientSideCredentials = $this->getProcessorTypeMetadata('client_side_credentials');
    if ($clientSideCredentials) {
      foreach (array_keys($clientSideCredentials) as $clientSideCredential) {
        if (!empty($this->_paymentProcessor[$clientSideCredential])) {
          $isClientSideEncrypted = TRUE;
        }
      }
    }
    return $isClientSideEncrypted;
  }

  /**
   * @param array $params
   *
   * @return \Omnipay\Common\Message\ResponseInterface
   *
   * @throws \CRM_Core_Exception
   */
  protected function doPreApproveForRecurring($params) {
    $currency = $this->getCurrency($params);
    if (!$currency) {
      throw new CRM_Core_Exception(ts('No currency specified'));
    }

    // https://developer.paypal.com/docs/api/payments.billing-plans/v1/#definition-merchant_preferences
    $response = $this->gateway->createCard($this->getCreditCardOptions(array_merge($params, ['action' => 'Authorize'])))->send();
    if (!$response->isSuccessful()) {
      throw new CRM_Core_Exception($response->getMessage());
    }
    return $response;
  }

  /**
   * @param $params
   *
   * @return false|string
   */
  protected function getCreditCardExpiry($params) {
    if (empty($params['credit_card_exp_date'])) {
      return FALSE;
    }
    return date("Y-m-t", strtotime($params['credit_card_exp_date']['Y'] . '-' . $params['credit_card_exp_date']['M']));
  }

  /**
   * Do a token payment.
   *
   * We might have a token due to form tokenisation or because we are processing
   * a repeat payment against a payment token (ie this would be the case with some recurring payments).
   *
   * Currently used by:
   *
   * Authorize.net - supports javascript tokenised payments
   * Eway Rapid Shared WithClientSideEncryption - supports javascript tokenised payments
   * Eway - all forms - support tokenised recurring payments
   * Payment Express - all forms - support tokenised recurring payments
   * PaypalRest - supports javascript tokenised payments.
   *
   * @param array $params
   *
   * @return array
   */
  protected function doTokenPayment(&$params) {
    if (!empty($params['is_recur'])) {
      $this->doRecurPostApproval($params);
    }
    // and, at least with Way rapid, the createCreditCard call ignores any attempt to authorise.
    // that is likely to be a pattern.
    $action = CRM_Utils_Array::value('payment_action', $params, 'purchase');
    // This is a bit tricky. With Paypal there are 2 flows
    // 1) you get a token from paypal checkout but there is no recurring - this token needs to be 'completed'
    // 2) you have a recurring payment token that we can bill against. However, is_recur is not
    // currently we are using a bit of black magic & setting payment_action in the
    // ProcessRecurring function  - that has test coverage.
    // Perhaps we need to set a one-time-token field or incomplete_token field in the first flow?
    // @todo - revisit.
    if (method_exists($this->gateway, 'completePurchase') && !isset($params['payment_action']) && empty($params['is_recur'])) {
      $action = 'completePurchase';
    }

    $params['transactionReference'] = ($params['token']);
    $response = $this->gateway->$action($this->getCreditCardOptions(array_merge($params, ['cardTransactionType' => 'continuous'])))
      ->send();
    $this->logHttpTraffic();
    return $response;
  }

  /**
   * Get the bodies of the requests sent via Guzzle.
   *
   * @return array
   */
  public function getRequestBodies() {
    $transactions = $this->history->getAll();
    $requests = [];
    foreach ($transactions as $transaction) {
      $requests[] = (string) $transaction['request'];
    }
    return $requests;
  }

  /**
   * Get the bodies of the responses returned via Guzzle.
   *
   * @return array
   */
  public function getResponseBodies() {
    $responses = [];
    $transactions = $this->history->getAll();
    foreach ($transactions as $transaction) {
      $responses[] = (string) $transaction['response'];
    }
    return $responses;
  }

  /**
   * Log debugging data.
   *
   * @param $type
   * @param $details
   */
  public function log($type, $details) {
    if (\Civi::settings()->get('omnipay_developer_mode')) {
      $this->getLog()->debug('omnipay_' . $type, $details);
      $this->logHttpTraffic();
    }
  }

  /**
   * Get help text information (help, description, etc.) about this payment,
   * to display to the user.
   *
   * @param string $context
   *   Context of the text.
   *   Only explicitly supported contexts are handled without error.
   *   Currently supported:
   *   - contributionPageRecurringHelp (params: is_recur_installments, is_email_receipt)
   *
   * @param array $params
   *   Parameters for the field, context specific.
   *
   * @return string
   */
  public function getText($context, $params) {
    switch ($context) {
      case 'contributionPageContinueText' :
        return ts('Click <strong>Continue</strong> to finalise your payment');
        break;

    }
  }

}

