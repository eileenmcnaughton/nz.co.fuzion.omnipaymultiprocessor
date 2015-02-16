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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2014
 * $Id$
 *
 */
class CRM_Core_Payment_OmnipayMultiProcessor extends CRM_Core_Payment_PaymentExtended {
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var CRM_Core_Payment_Omnipay
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
  protected $_configurationFields = array('user_name', 'password', 'signature', 'subject');

  /**
   * Omnipay gateway
   * @var Omnipay\Common\AbstractGateway
   */
  protected $gateway;


  /**
   * singleton function used to manage this object
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
   * express checkout code. Check PayPal documentation for more information
   *
   * @param  array $params assoc array of input parameters for this transaction
   *
   * @return array the result in an nice formatted array (or an error object)
   * @public
   */
  function setExpressCheckOut(&$params) {
  }


  /**
   * do the express checkout at paypal. Check PayPal documentation for more information
   *
   * @param $params
   *
   * @internal param string $token the key associated with this transaction
   *
   * @return array the result in an nice formatted array (or an error object)
   * @public
   */
  function doExpressCheckout(&$params) {
  }

  /**
   * This function collects all the information from a web/api form and invokes
   * the relevant payment processor specific functions to perform the transaction
   *
   * @param  array $params assoc array of input parameters for this transaction
   *
   * @param string $component
   *
   * @throws CRM_Core_Exception
   * @return array the result in an nice formatted array (or an error object)
   * @public
   */
  function doDirectPayment(&$params, $component = 'contribute') {
    $this->_component = strtolower($component);
    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $this->_paymentProcessor['payment_processor_type']));
    $this->setProcessorFields();
    $this->setTransactionID(CRM_Utils_Array::value('contributionID', $params));
    $this->storeReturnUrls($params['qfKey'], CRM_Utils_Array::value('participantID', $params), CRM_Utils_Array::value('eventID', $params));
    $this->saveBillingAddressIfRequired($params);

    try {
      $response = $this->gateway->purchase($this->getCreditCardOptions($params, $component))->send();
      if ($response->isSuccessful()) {
        // mark order as complete
        $params['trxn_id'] = $response->getTransactionReference();
        //gross_amount ? fee_amount?
        return $params;
      }
      elseif ($response->isRedirect()) {
        if ($response->isTransparentRedirect() || !empty($this->gateway->transparentRedirect)) {
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
        return $this->handleError('alert','failed processor transaction ' . $this->_paymentProcessor['payment_processor_type'], (array) $response, 9001, $response->getMessage());
      }
    } catch (\Exception $e) {
      // internal error, log exception and display a generic message to the customer
      //@todo - looks like invalid credit card numbers are winding up here too - we could handle separately by capturing that exception type - what is good fraud practice?
      return $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], array($e->getCode() => $e->getMessage()), $e->getCode(), 'Sorry, there was an error processing your payment. Please try again later.');
    }
  }

  /**
   * Set fields on payment processor based on the labels in the payment_processor_type table & the values in the payment_processor table
   * @throws CRM_Core_Exception
   */
  function setProcessorFields() {
    $fields = $this->getProcessorFields();
    try {
      foreach ($fields as $name => $value) {
        $fn = "set{$name}";
        $this->gateway->$fn($value);
      }
      if (in_array('testMode', array_keys($this->gateway->getDefaultParameters()))) {
        $this->gateway->setTestMode($this->_is_test);
      }
    }
    catch (Exception $e) {
      throw new CRM_Core_Exception('Processor is incorrectly configured');
    }
  }

  /**
   * get array of payment processor configuration fields keyed by the relevant payment processor properties
   * For example the field might be displayed as 'Secret Key' - the payment processor property is secretKey
   * We will give 'ID special treatment as it would be pretty ugly to present the end user with a label saying 'Id'
   *
   * @return array payment processor configuration fields
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
   * Core specifically won't save billing address if you use notify mode so we make up for that here
   * For example a transparent Redirect (POST credit card form off-site) processor has some features of each.
   *
   * Rather than try to categorise the processor we say 'if a contribution exists and it does not have a billing address but we have billing
   * fields in our form we will create a billing address for the payment
   *
   * @param array $params
   */
  function saveBillingAddressIfRequired($params) {
    if (!empty($params['contributionID']) && $this->hasBillingAddressFields($params)) {
      $contribution = civicrm_api3('contribution', 'getsingle', array('id' => $params['contributionID'], 'return' => 'address_id, contribution_status_id'));
      if (empty($contribution['address_id'])) {
        civicrm_api3('contribution', 'create', array(
          'id' => $params['contributionID'],
          'contribution_status_id' => $contribution['contribution_status_id'],// required due to CRM-15105
          'address_id' => CRM_Contribute_BAO_Contribution::createAddress($params, CRM_Core_BAO_LocationType::getBilling())
        ));
      }
    }
  }

  /**
   * Are there any billing address fields in the params array (not including billing-first-name - only true address fields)
   * @param $params
   *
   * @return bool
   */
  function hasBillingAddressFields($params) {
    $billingFields = array_intersect_key($params, array_flip($this->getBillingAddressFields()));
    return !empty($billingFields);
  }

  /**
   * get fieldname in format used in gateway functions $this->gateway->setSecretKey
   * We remove spaces & camel it
   * @param $fieldName
   *
   * @return mixed
   */
  function camelFieldName($fieldName) {
    return str_replace(' ', '', ucwords(strtolower($fieldName)));
  }

  /**
   * Generate card object from CiviCRM params
   * At this stage we are not yet mapping
   * - startMonth
   * - startYear
   * - issueNumber
   * - frequency_interval,
   * - frequency_day,
   * - frequency_installments
   * - shipping fields
   *
   * @param $params
   *
   * @return array
   */
  function getCreditCardObjectParams($params) {
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
      'billingPhone' => 'phone', // we don't specifically anticipate phone to come through - adding this & company as 'best guess'
      'company' => 'organization_name',
      'type' => 'credit_card_type',
    );

    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
    }

    if(empty($cardFields['email'])) {
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
    if(is_numeric($cardFields['billingCountry'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::countryIsoCode($cardFields['billingCountry']);
    }
    if(is_numeric($cardFields['billingState'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::stateProvince($cardFields['billingState']);
    }
    return $cardFields;
  }

  function getSensitiveCreditCardObjectOptions($params) {
    $basicMappings = array(
      'cvv' => 'cvv2',
      'number' => 'credit_card_number',
    );
    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
    }
    if(!empty($params['credit_card_exp_date'])) {
      $cardFields['expiryMonth'] = $params['credit_card_exp_date']['M'];
      $cardFields['expiryYear'] = $params['credit_card_exp_date']['Y'];
    }
    return $cardFields;
  }
  /**
   * Get options for credit card
   * Not yet implemented
   * - token
   *
   * @param $params
   * @param $component
   *
   * @return array
   */
  function getCreditCardOptions($params, $component) {
    // Contribution page in 4.4 passes amount - not sure which passes total_amount if any.
    if(isset($params['total_amount'])) {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['total_amount']);
    }
    else {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['amount']);
    }
    $creditCardOptions = array(
      'amount' => $amount,
      // Contribution page in 4.4 passes currencyID - not sure which passes currency (if any).
      'currency' => strtoupper(!empty($params['currencyID']) ? $params['currencyID'] : $params['currency']),
      'description' => $this->getPaymentDescription($params),
      'transactionId' => $this->transaction_id,
      'clientIp' => CRM_Utils_System::ipAddress(),
      'returnUrl' => $this->getReturnSuccessUrl($params['qfKey']),
      'cancelUrl' => $this->getCancelUrl($params['qfKey'], CRM_Utils_Array::value('participantID', $params)),
      'notifyUrl' => $this->getNotifyUrl(),
      'card' => $this->getCreditCardObjectParams($params),
    );
    CRM_Utils_Hook::alterPaymentProcessorParams($this, $params, $creditCardOptions);
    $creditCardOptions['card'] = array_merge($creditCardOptions['card'], $this->getSensitiveCreditCardObjectOptions($params));
    return $creditCardOptions;
  }

  /**
   * This function checks to see if we have the right config values
   *
   * @return string the error message if any
   * @public
   */
  function checkConfig() {
    //@todo check gateway against $this->gateway->getDefaultParameters();
  }

  /**
   * implement doTransferCheckout. We treat transfer checkouts the same as direct payments & rely on our
   * abstracted library to action the differences
   *
   * @param $params
   * @param string $component
   *
   * @throws CRM_Core_Exception
   */
  function doTransferCheckout(&$params, $component = 'contribute') {
    $this->doDirectPayment($params, $component);
    throw new CRM_Core_Exception('Payment redirect failed');
  }

  /**
   * Get billing fields required for this block
   * @todo move this metadata requirement onto the class - or the mgd files
   * @return array
   */
  function getBillingBlockFields() {
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
   * Get the fields to display for transparent direct method
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
  function getTransparentDirectDisplayFields() {
    $corePaymentFields = $this->getCorePaymentFields();
    $paymentFieldMappings = $this->getPaymentFieldMapping();
    foreach ($paymentFieldMappings as $fieldName => $fieldSpec) {
      $paymentFieldMappings[$fieldName] = array_merge($corePaymentFields[$fieldSpec['core_field_name']], $fieldSpec);
    }
    return $paymentFieldMappings;
  }

  /**
   * we are just getting the cybersource specific mapping for now - see comments on
   * getTransparentDirectDisplayFields
   * @return array
   */
  function getPaymentFieldMapping() {
    return array(
      'card_type' => array(
        'core_field_name' => 'credit_card_type',
        'attributes' => array(
          '' => ts('- select -'),
          '001' => 'Visa',
          '002' => 'Mastercard',
          '003' => 'Amex',
          '004' => 'Discover',
        )
      ),
      'card_number' => array('core_field_name' => 'credit_card_number',),
      'card_expiry_date' => array('core_field_name' => 'credit_card_exp_date'),
    );
  }

  /**
   * get core CiviCRM payment fields
   * @return array
   */
  function getCorePaymentFields() {
    $creditCardType = array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::creditCard();
    return array(
      'credit_card_number' => array(
        'htmlType' => 'text',
        'name' => 'credit_card_number',
        'title' => ts('Card Number'),
        'cc_field' => TRUE,
        'attributes' => array('size' => 20, 'maxlength' => 20, 'autocomplete' => 'off'),
        'is_required' => TRUE,
      ),
      'cvv2' => array(
        'htmlType' => 'text',
        'name' => 'cvv2',
        'title' => ts('Security Code'),
        'cc_field' => TRUE,
        'attributes' => array('size' => 5, 'maxlength' => 10, 'autocomplete' => 'off'),
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
      )
    );
  }

  /**
   * Get core CiviCRM address fields.
   *
   * @return array
   */
  function getBillingAddressFields() {
    $billingFields = array();
    foreach (array('street_address', 'city', 'state_province_id', 'postal_code', 'country_id',) as $addressField) {
      $billingFields[$addressField]  = 'billing_' . $addressField . '-' . CRM_Core_BAO_LocationType::getBilling();
    }
    return $billingFields;
  }

  /**
   * handle response from processor. We simply get the params from the REQUEST and pass them to a static function that
   * can also be called / tested outside the normal process
   */
  public function handlePaymentNotification() {
    $params = $_REQUEST;
    $paymentProcessorID = $params['processor_id'];
    $this->_paymentProcessor = civicrm_api3('payment_processor', 'getsingle', array('id' => $paymentProcessorID));
    $this->_paymentProcessor['name'] = civicrm_api3('payment_processor_type', 'getvalue', array('id' => $this->_paymentProcessor['payment_processor_type_id'], 'return' => 'name'));
    $this->processPaymentNotification($params);
  }

  /**
   * Update Transaction based on outcome of the API
   * @param $params
   *
   * @throws CRM_Core_Exception
   * @throws CiviCRM_API3_Exception
   */
  public function processPaymentNotification($params) {

    $this->gateway = Omnipay::create(str_replace('omnipay_', '', $this->_paymentProcessor['name']));
    $this->setProcessorFields();
    $originalRequest = $_REQUEST;
    $_REQUEST = $params;
    $response = $this->gateway->completePurchase($params)->send();
    if ($response->getTransactionReference()) {
      $this->setTransactionID($response->getTransactionReference());
    }
    if ($response->isSuccessful()) {
      try {
        //cope with CRM14950 not being implemented
        $contribution = civicrm_api3('contribution', 'getsingle', array('id' => $this->transaction_id, 'return' => 'contribution_status_id'));
        if ($contribution['contribution_status_id'] != CRM_Core_OptionGroup::getValue('contribution_status', 'Completed', 'name')) {
          civicrm_api3('contribution', 'completetransaction', array('id' => $this->transaction_id));
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        if (!stristr($e->getMessage(), 'Contribution already completed')) {
          $this->handleError('error', $this->transaction_id  . $e->getMessage(), 'ipn_completion', 9000, 'An error may have occurred. Please check your receipt is correct');
        }
      }
      $_REQUEST = $originalRequest;
      CRM_Utils_System::redirect($this->getStoredUrl('success'));
    }
    elseif ($this->transaction_id) {
      civicrm_api3('contribution', 'create', array('id' => $this->transaction_id, 'contribution_status_id' => 'Failed'));
    }
    $userMessage = $response->getMessage();
    if (method_exists($response, 'getInvalidFields') && ($invalidFields = $response->getInvalidFields()) != array()) {
      $userMessage = ts('Invalid data entered in fields ' . implode(', ', $invalidFields));
    }

    $this->handleError('error', $this->transaction_id  . ' ' . $response->getMessage(), 'processor_error', 9002, $userMessage);

    $_REQUEST = $originalRequest;
    CRM_Utils_System::redirect($this->getStoredUrl('fail'));
  }

  /**
   * Static wrapper for IPN / Payment response handling - this allows us to re-call from the api
   * @param $params
   *
   * @return bool
   * @throws CiviCRM_API3_Exception
   */
  static function processPaymentResponse($params) {
    $processor =  civicrm_api3('payment_processor', 'getsingle', array('id' => $params['processor_id']));
    $responder = new CRM_Core_Payment_OmnipayMultiProcessor('live', $processor);
    $responder->processPaymentNotification($params);
    return TRUE;
  }
}

