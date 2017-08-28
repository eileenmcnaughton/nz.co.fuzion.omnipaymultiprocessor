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
  use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class CRM_Core_Payment_OmnipayMultiProcessor.
 */
class CRM_Core_Payment_OmnipayMultiProcessor extends CRM_Core_Payment_PaymentExtended {
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable.
   *
   * This is redundant from 4.4.
   *
   * @var CRM_Core_Payment_OmnipayMultiProcessor
   * @static
   */
  static private $_singleton = NULL;

  /**
   * For code clarity declare is_test as a boolean
   * @var bool
   */
  protected $_is_test = FALSE;

  /**
   * names of fields in payment processor table that relate to configuration of the processor instance
   * @var array
   */
  protected $_configurationFields = array(
    'user_name',
    'password',
    'signature',
    'subject',
  );

  /**
   * Omnipay gateway
   * @var Omnipay\Common\AbstractGateway
   */
  protected $gateway;


  /**
   * Singleton function used to manage this object.
   *
   * Redundant from 4.6.
   *
   * @param string $mode the mode of operation: live or test
   *
   * @param array $paymentProcessor
   * @param null $paymentForm
   * @param bool $force
   *
   * @return object
   * @static
   */
  static function &singleton($mode = 'test', &$paymentProcessor, &$paymentForm = NULL, $force = FALSE) {
    if (!empty($paymentProcessor['id'])) {
      $cacheKey = $paymentProcessor['id'];
    }
    else {
      //@todo eliminated instances of this in favour of id-specific instances.
      $cacheKey = $mode . '_' . $paymentProcessor['name'];
    }
    if (!isset(self::$_singleton[$cacheKey]) || self::$_singleton[$cacheKey] === NULL) {
      self::$_singleton[$cacheKey] = new CRM_Core_Payment_Omnipaymultiprocessor($mode, $paymentProcessor);
    }
    return self::$_singleton[$cacheKey];
  }

  /**
   * Express checkout code.
   *
   * Check PayPal documentation for more information
   *
   * @param array $params assoc array of input parameters for this transaction
   *
   * @return array
   *   The result in an nice formatted array (or an error object)
   */
  public function setExpressCheckOut(&$params) {
  }


  /**
   * Do the express checkout at paypal.
   *
   * Check PayPal documentation for more information
   *
   * @param array $params
   */
  public function doExpressCheckout(&$params) {
  }

  /**
   * Process payment with external gateway.
   *
   * @param array $params assoc array of input parameters for this transaction
   *
   * @param string $component
   *
   * @throws CRM_Core_Exception
   * @return array
   *   The result in an nice formatted array (or an error object)
   */
  public function doDirectPayment(&$params, $component = 'contribute') {
    $this->_component = strtolower($component);
    $this->ensurePaymentProcessorTypeIsSet();
    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $this->_paymentProcessor['payment_processor_type']));
    $this->setProcessorFields();
    $this->setTransactionID(CRM_Utils_Array::value('contributionID', $params));
    $this->storeReturnUrls($params['qfKey'], CRM_Utils_Array::value('participantID', $params), CRM_Utils_Array::value('eventID', $params));
    $this->saveBillingAddressIfRequired($params);

    try {
      if (!empty($params['is_recur'])) {
        $response = $this->gateway->createCard($this->getCreditCardOptions(array_merge($params, array('action' => 'Purchase')), $component))->send();
      }
      elseif (!empty($params['token'])) {
        $params['transactionReference'] = ($params['token']);
        $response = $this->gateway->capture($this->getCreditCardOptions($params, $component))
          ->send();
      }
      else {
        $response = $this->gateway->purchase($this->getCreditCardOptions($params, $component))
          ->send();
      }
      if ($response->isSuccessful()) {
        // mark order as complete
        $params['trxn_id'] = $response->getTransactionReference();
        //gross_amount ? fee_amount?
        return $params;
      }
      elseif ($response->isRedirect()) {
        $isTransparentRedirect = ($response->isTransparentRedirect() || !empty($this->gateway->transparentRedirect));
        // Unset $this->gateway before storing session to cache due to risk of
        // Serialization of 'Closure' is not allowed error - issue #17
        $this->gateway = NULL;
        CRM_Core_Session::storeSessionObjects(FALSE);
        if ($response->isTransparentRedirect()) {
          $this->storeTransparentRedirectFormData($params['qfKey'], $response->getRedirectData() + array(
            'payment_processor_id' => $this->_paymentProcessor['id'],
            'post_submit_url' => $response->getRedirectURL(),
          ));
          CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/payment/details', array('key' => $params['qfKey'])));
        }
        $response->redirect();
      }
      else {
        //@todo - is $response->getCode supported by some / many processors?
        return $this->handleError('alert', 'failed processor transaction ' . $this->_paymentProcessor['payment_processor_type'], (array) $response, 9001, $response->getMessage());
      }
    }
    catch (\Exception $e) {
      // internal error, log exception and display a generic message to the customer
      //@todo - looks like invalid credit card numbers are winding up here too - we could handle separately by capturing that exception type - what is good fraud practice?
      return $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], array($e->getCode() => $e->getMessage()), $e->getCode(), 'Sorry, there was an error processing your payment. Please try again later.');
    }
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
        $this->gateway->$fn($value);
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
    $labelFields = $result = array();
    foreach ($this->_configurationFields as $configField) {
      if (!empty($this->_paymentProcessor[$configField])) {
        $labelFields[$configField] = "{$configField}_label";
      }
    }
    $processorFields = civicrm_api3('payment_processor_type', 'getsingle', array(
      'id' => $this->_paymentProcessor['payment_processor_type_id'],
      'return' => $labelFields)
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
      $contribution = civicrm_api3('contribution', 'getsingle', array('id' => $params['contributionID'], 'return' => 'address_id, contribution_status_id'));
      if (empty($contribution['address_id'])) {
        civicrm_api3('contribution', 'create', array(
          'id' => $params['contributionID'],
          // required due to CRM-15105
          'contribution_status_id' => $contribution['contribution_status_id'],
          'address_id' => CRM_Contribute_BAO_Contribution::createAddress($params, CRM_Core_BAO_LocationType::getBilling())
        ));
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
    $cardFields = array();
    $basicMappings = array(
      'firstName' => 'billing_first_name',
      'lastName' => 'billing_last_name',
      'currency' => 'currency',
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
    );

    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
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
    //do we need these if clauses lines? in 4.5 contribution page we don't....
    if (is_numeric($cardFields['billingCountry'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::countryIsoCode($cardFields['billingCountry']);
    }
    if (is_numeric($cardFields['billingState'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::stateProvince($cardFields['billingState']);
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
    $basicMappings = array(
      'cvv' => 'cvv2',
      'number' => 'credit_card_number',
    );
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
   * @param string $component
   *
   * @return array
   */
  private function getCreditCardOptions($params, $component) {
    // Contribution page in 4.4 passes amount - not sure which passes total_amount if any.
    if (isset($params['total_amount'])) {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['total_amount']);
    }
    else {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['amount']);
    }
    $creditCardOptions = array(
      'amount' => $amount,
      // Contribution page in 4.4 (confirmed Event online, 4.7) passes currencyID - not sure which passes currency (if any).
      'currency' => strtoupper(!empty($params['currencyID']) ? $params['currencyID'] : $params['currency']),
      'description' => $this->getPaymentDescription($params),
      'transactionId' => $this->transaction_id,
      'clientIp' => CRM_Utils_System::ipAddress(),
      'returnUrl' => $this->getNotifyUrl(TRUE),
      'cancelUrl' => $this->getCancelUrl($params['qfKey'], CRM_Utils_Array::value('participantID', $params)),
      'notifyUrl' => $this->getNotifyUrl(),
      'card' => $this->getCreditCardObjectParams($params),
      'cardReference' => CRM_Utils_Array::value('token', $params),
      'transactionReference' => CRM_Utils_Array::value('token', $params),
    );
    if (!empty($params['action'])) {
      $creditCardOptions['action'] = 'Purchase';
    }
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
   * Implement doTransferCheckout.
   *
   * We treat transfer checkouts the same as direct payments & rely on our
   * abstracted library to action the differences
   *
   * @param array $params
   * @param string $component
   *
   * @throws CRM_Core_Exception
   */
  public function doTransferCheckout(&$params, $component = 'contribute') {
    $this->doDirectPayment($params, $component);
    if (!empty($params['token'])) {
      // It is possible no redirect was required here.
      return;
    }
    throw new CRM_Core_Exception('Payment redirect failed');
  }

  /**
   * Get array of fields that should be displayed on the payment form.
   *
   * This function is new to 4.6 and will not be called on lower versions.
   *
   * @return array
   *
   * @throws CiviCRM_API3_Exception
   */
  public function getPaymentFormFields() {
    if ($this->_paymentProcessor['billing_mode'] == 4 || $this->isTransparentRedirect()) {
      return array();
    }
    return $this->_paymentProcessor['payment_type'] == 1 ? $this->getCreditCardFormFields() : $this->getDirectDebitFormFields();
  }

  /**
   * Get billing fields required for this block.
   *
   * @todo move this metadata requirement onto the class - or the mgd files
   * @return array
   */
  public function getBillingBlockFields() {
    $billingID = $locationTypes = CRM_Core_BAO_LocationType::getBilling();
    //for now we will cheat & just use the really blunt characteristics option - ie.
    // ie billing mode 1 or payment type 3 get billing fields.
    // we really want this to be metadata of the payment processors
    if ($this->_paymentProcessor['billing_mode'] != 1 && $this->_paymentProcessor['payment_type'] != 3) {
      return array();
    }
    return array(
      'first_name' => 'billing_first_name',
      //'middle_name' => 'billing_middle_name',
      'last_name' => 'billing_last_name',
      'street_address' => "billing_street_address-{$billingID}",
      'city' => "billing_city-{$billingID}",
      'country' => "billing_country_id-{$billingID}",
      'state_province' => "billing_state_province_id-{$billingID}",
      'postal_code' => "billing_postal_code-{$billingID}",
    );
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
   * at this stage we only have the scenario of cybersource - later we can get metadata from
   * omnipay depending how it develops or use our own yml file (if we go down the yml path the mgd.php file
   * should parse it to avoid duplication https://groups.google.com/forum/#!topic/omnipay/hjxBCU5blaU
   * @return array
   */
  public function getTransparentDirectDisplayFields() {
    $corePaymentFields = $this->getCorePaymentFields();
    $paymentFieldMappings = $this->getPaymentFieldMapping();
    foreach ($paymentFieldMappings as $fieldName => $fieldSpec) {
      $paymentFieldMappings[$fieldName] = array_merge($corePaymentFields[$fieldSpec['core_field_name']], $fieldSpec);
    }
    return $paymentFieldMappings;
  }

  /**
   * Get mapping for payment fields.
   *
   * We are just getting the cybersource specific mapping for now - see comments on
   * getTransparentDirectDisplayFields.
   *
   * @return array
   */
  private function getPaymentFieldMapping() {
    return array(
      'card_type' => array(
        'core_field_name' => 'credit_card_type',
        'options' => array(
          '' => ts('- select -'),
          '001' => 'Visa',
          '002' => 'Mastercard',
          '003' => 'Amex',
          '004' => 'Discover',
        ),
      ),
      'card_number' => array('core_field_name' => 'credit_card_number'),
      'card_expiry_date' => array('core_field_name' => 'credit_card_exp_date'),
      'card_cvn' => array('core_field_name' => 'cvv2'),
    );
  }

  /**
   * Get core CiviCRM payment fields.
   *
   * @return array
   */
  private function getCorePaymentFields() {
    $creditCardType = array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::creditCard();
    return array(
      'credit_card_number' => array(
        'htmlType' => 'text',
        'name' => 'credit_card_number',
        'title' => ts('Card Number'),
        'cc_field' => TRUE,
        'attributes' => array(
          'size' => 20,
          'maxlength' => 20,
          'autocomplete' => 'off',
        ),
        'is_required' => TRUE,
      ),
      'cvv2' => array(
        'htmlType' => 'text',
        'name' => 'cvv2',
        'title' => ts('Security Code'),
        'cc_field' => TRUE,
        'attributes' => array(
          'size' => 5,
          'maxlength' => 10,
          'autocomplete' => 'off',
        ),
        'is_required' => TRUE,
      ),
      'credit_card_exp_date' => array(
        'htmlType' => 'date',
        'name' => 'credit_card_exp_date',
        'title' => ts('Expiration Date'),
        'cc_field' => TRUE,
        'attributes' => CRM_Core_SelectValues::date('creditCard'),
        'is_required' => TRUE,
      ),
      'credit_card_type' => array(
        'htmlType' => 'select',
        'name' => 'credit_card_type',
        'title' => ts('Card Type'),
        'cc_field' => TRUE,
        'attributes' => $creditCardType,
        'is_required' => FALSE,
      ),
    );
  }

  /**
   * Get core CiviCRM address fields.
   *
   * @param int $billingLocationID
   *
   * @return array
   */
  public function getBillingAddressFields($billingLocationID = NULL) {
    if (!$this->isTransparentRedirect()) {
      return parent::getBillingAddressFields($billingLocationID);
    }
    $billingFields = array(
      'first_name' => 'billing_first_name',
      'middle_name' => 'billing_middle_name',
      'last_name' => 'billing_last_name',
    );
    foreach (array(
               'street_address',
               'city',
               'state_province_id',
               'postal_code',
               'country_id',
             ) as $addressField) {
      $billingFields[$addressField]  = 'billing_' . $addressField . '-' . CRM_Core_BAO_LocationType::getBilling();
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
    $this->_paymentProcessor = civicrm_api3('payment_processor', 'getsingle', array('id' => $paymentProcessorID));
    $this->_paymentProcessor['name'] = civicrm_api3('payment_processor_type', 'getvalue', array('id' => $this->_paymentProcessor['payment_processor_type_id'], 'return' => 'name'));
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
    try {
      $response = $this->gateway->completePurchase($params)->send();
      if ($response->getTransactionId()) {
        $this->setTransactionID($response->getTransactionId());
      }
    }
    catch (\Omnipay\Common\Exception\InvalidRequestException $e) {
      $q = explode('/', CRM_Utils_Array::value(CRM_Core_Config::singleton()->userFrameworkURLVar, $_GET, ''));
      array_pop($q);
      $this->setTransactionID(array_pop($q));
      if (!civicrm_api3('Contribution', 'getcount', array(
        'id' => $this->transaction_id,
        'contribution_status_id' => array('IN' => array('Completed', 'Pending'))
      ))) {
        $this->redirectOrExit('fail');
      }
      $this->redirectOrExit('success');
    }

    if ($response->isSuccessful()) {
      try {
        //cope with CRM14950 not being implemented
        $contribution = civicrm_api3('contribution', 'getsingle', array(
          'id' => $this->transaction_id,
          //'return' => 'contribution_status_id, contribution_recur_id, contact_id, contribution_contact_id',
        ));

        if ($contribution['contribution_status_id'] != CRM_Core_OptionGroup::getValue('contribution_status', 'Completed', 'name')) {
          civicrm_api3('contribution', 'completetransaction', array(
            'id' => $this->transaction_id,
            'trxn_id' => $response->getTransactionReference(),
            'payment_processor_id' => $params['processor_id'],
          ));
        }
        if (!empty($contribution['contribution_recur_id']) && ($tokenReference = $response->getCardReference()) != FALSE) {
          $this->storePaymentToken($params, $contribution, $tokenReference);
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        if (!stristr($e->getMessage(), 'Contribution already completed')) {
          $this->handleError('error', 'ipn_completion failed', $this->transaction_id  . $e->getMessage(), 9000, 'An error may have occurred. Please check your receipt is correct');
        }
      }
      $_REQUEST = $originalRequest;
      $this->redirectOrExit('success');
    }
    elseif ($this->transaction_id) {
      civicrm_api3('contribution', 'create', array('id' => $this->transaction_id, 'contribution_status_id' => 'Failed'));
    }
    $userMessage = ts('The transaction was not processed. The message from the bank was : %1. Please try again', array(1 => $response->getMessage()));
    if (method_exists($response, 'getInvalidFields') && ($invalidFields = $response->getInvalidFields()) != array()) {
      $userMessage = ts('Invalid data entered in fields ' . implode(', ', $invalidFields));
    }

    try {
      $this->handleError('error', $this->transaction_id . ' ' . $response->getMessage(), 'processor_error', 9002, $userMessage);
    }
    catch (\Civi\Payment\Exception\PaymentProcessorException $e) {

    }

    $_REQUEST = $originalRequest;
    $this->redirectOrExit('fail');
  }

  public function queryPaymentPlans($params) {
    $this->createGateway($this->_paymentProcessor['id']);
    $response = $this->gateway->paymentPlansQuery($params)->send();
    return $response->getPlanData();
  }

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
   */
  public static function processPaymentResponse($params) {
    $processor = civicrm_api3('payment_processor', 'getsingle', array('id' => $params['processor_id']));
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
   */
  protected function redirectOrExit($outcome) {
    if ($outcome === 'fail') {
      CRM_Core_Session::setStatus(ts('Your payment was not successful. Please try again'));
    }

    if (($success_url = $this->getStoredUrl($outcome)) != FALSE) {
      CRM_Utils_System::redirect($success_url);
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * Store a payment token.
   *
   * From 4.6 onwards the payment token table can store these tokens.
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
    $token = civicrm_api3('payment_token', 'create', array(
      'contact_id' => $contribution['contact_id'],
      'payment_processor_id' => $params['processor_id'],
      'token' => $tokenReference,
      'is_transactional' => FALSE,
      'created_id' => (CRM_Core_Session::singleton()->getLoggedInContactID() ? : $contribution['contact_id']),
    ));
    $contributionRecur = civicrm_api3('ContributionRecur', 'getsingle', array('id' => $contributionRecurID));
    civicrm_api3('contribution_recur', 'create', array(
      'id' => $contributionRecurID,
      'payment_token_id' => $token['id'],
      'is_transactional' => FALSE,
      'next_sched_contribution_date' => CRM_Utils_Date::isoToMysql(
        date('Y-m-d 00:00:00', strtotime('+' . $contributionRecur['frequency_interval'] . ' ' . $contributionRecur['frequency_unit']))
      ),
    ));
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
      $this->_paymentProcessor['payment_processor_type'] = civicrm_api3('PaymentProcessorType', 'getvalue', array(
        'id' => $this->_paymentProcessor['payment_processor_type_id'],
        'return' => 'name',
      ));
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
    $paymentType = civicrm_api3('option_value', 'getsingle', array(
      'value' => $this->_paymentProcessor['payment_type'],
      'option_group_id' => 'payment_type'
    ));
    if ($paymentType['name'] == 'credit_card_off_site_post') {
      return TRUE;
    }
  }

  /**
   * Should the first payment date be configurable when setting up back office recurring payments.
   * In the case of Authorize.net this is an option
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
    $entities = array();
    omnipaymultiprocessor_civicrm_managed($entities);
    foreach ($entities as $entity) {
      if ($entity['entity'] === 'payment_processor_type') {
        if (
        $entity['params']['name'] === $this->_paymentProcessor['payment_processor_type']
        && !empty($entity['params']['supports_preapproval'])) {
          return TRUE;
        }
      }
    }
    return FALSE;
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
    $this->_component = 'contribute';
    $this->ensurePaymentProcessorTypeIsSet();
    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $this->_paymentProcessor['payment_processor_type']));
    $this->setProcessorFields();
    $this->setTransactionID(CRM_Utils_Array::value('contributionID', $params));
    $this->storeReturnUrls($params['qfKey'], CRM_Utils_Array::value('participantID', $params), CRM_Utils_Array::value('eventID', $params));
    $this->saveBillingAddressIfRequired($params);

    try {
      $response = $this->gateway->authorize($this->getCreditCardOptions($params, 'contribute'))
        ->send();
      if ($response->isSuccessful()) {
        $params['trxn_id'] = $params['token'] = $response->getTransactionReference();
        $creditCardPan = '************' . substr($params['credit_card_number'], -4);
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
        return array(
          'pre_approval_parameters' => array('token' => $response->getTransactionReference())
        );
      }
      else {
        $this->purgeSensitiveDataFromSession();
        unset($params['credit_card_number']);
        unset($params['cvv2']);
        return $this->handleError('alert', 'failed processor transaction ' . $this->_paymentProcessor['payment_processor_type'], (array) $response, 9001, $response->getMessage());
      }
    }
    catch (\Exception $e) {
      $this->purgeSensitiveDataFromSession();
      // internal error, log exception and display a generic message to the customer
      $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], array($e->getCode() => $e->getMessage()), $e->getCode(), 'Sorry, there was an error processing your payment. Please try again later.');
    }
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
   */
  public function getEditableRecurringScheduleFields() {
    $possibles = array('amount');
    $fields = civicrm_api3('ContributionRecur', 'getfields', array('action' => 'create'));
    // The html is only set in 4.7.11 +
    // The date fields look a bit funky at the moment so not adding all possible fields.
    if (!empty($fields['values']['next_sched_contribution_date']['html'])) {
      $possibles[] =  'next_sched_contribution_date';
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
    $paymentProcessorTypeId = civicrm_api3('payment_processor', 'getvalue', array(
      'id' => $id,
      'return' => 'payment_processor_type_id',
    ));
    $paymentProcessorTypeName = civicrm_api3('payment_processor_type', 'getvalue', array(
      'id' => $paymentProcessorTypeId,
      'return' => 'name',
    ));
    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $paymentProcessorTypeName));
    $this->setProcessorFields();
  }

  /**
   * Remove sensitive data from the session before it is stored.
   *
   * @return array
   */
  protected function purgeSensitiveDataFromSession() {
    foreach ($_SESSION as &$key) {
      if (isset($key['values']) && is_array($key['values'])) {
        foreach ($key['values'] as &$values) {
          foreach (array(
                     'credit_card_number',
                     'cvv2',
                     'credit_cate_type'
                   ) as $fieldName) {
            if (!empty($values[$fieldName])) {
              $values[$fieldName] = '';
            }
          }
          if (isset($values['credit_card_exp_date'])) {
            $values['credit_card_exp_date'] = array('M' => '', 'Y' => '');
          }
        }
      }
    }
    return array($key, $values);
  }

}

