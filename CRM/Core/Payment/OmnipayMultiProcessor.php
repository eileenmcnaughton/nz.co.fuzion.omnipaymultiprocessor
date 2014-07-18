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
   * @var object
   */
  protected $gateway;

  /**
   * singleton function used to manage this object
   *
   * @param string $mode the mode of operation: live or test
   *
   * @param object $paymentProcessor
   * @param null $paymentForm
   * @param bool $force
   *
   * @return object
   * @static
   */
  static function &singleton($mode = 'test', &$paymentProcessor, &$paymentForm = NULL, $force = FALSE) {
    $processorName = $paymentProcessor['name'];
    if (!isset(self::$_singleton[$processorName]) || self::$_singleton[$processorName] === NULL) {
      self::$_singleton[$processorName] = new CRM_Core_Payment_Omnipaymultiprocessor($mode, $paymentProcessor);
    }
    return self::$_singleton[$processorName];
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
    //remove this!!!!!
    $this->gateway->setTestMode(TRUE);

    try {
      $response = $this->gateway->purchase($this->getCreditCardOptions($params, $component))->send();
      if ($response->isSuccessful()) {
        // mark order as complete
        $params['trxn_id'] = $response->getTransactionReference();
        //gross_amount ? fee_amount?
        return $params;
      }
      elseif ($response->isRedirect()) {
        //ie off site processor
        echo $response->getRedirectResponse();
        $response->redirect();
      }
      else {
        //@todo - is $response->getCode supported by some / many processors?
        return $this->handleError('alert','failed processor transaction ' . $this->_paymentProcessor['payment_processor_type'], (array) $response, 9001, $response->getMessage());
      }
    } catch (\Exception $e) {
      // internal error, log exception and display a generic message to the customer
      //@todo - looks like invalid credit card numbers are winding up here too - we could handle separately by capturing that exception type - what is good fraud practice?
      return $this->handleError('error', 'unknown processor error ' . $this->_paymentProcessor['payment_processor_type'], array($e->getCode() => $e->getMessage()), 'Sorry, there was an error processing your payment. Please try again later.');
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
      if (in_array('testMode', array_keys($this->gateway->getDefaultParameters))) {
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
      'cvv' => 'cvv2',
      'number' => 'credit_card_number',
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
    //do we need these if clauses lines? in 4.5 contribution page we don't....
    if(is_numeric($cardFields['billingCountry'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::countryIsoCode($cardFields['billingCountry']);
    }
    if(is_numeric($cardFields['billingState'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::stateProvince($cardFields['billingState']);
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
    //contribution page in 4.4 passes amount - not sure which passes total_amount if any
    if(isset($params['total_amount'])) {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['total_amount']);
    }
    else {
      $amount = (float) CRM_Utils_Rule::cleanMoney($params['amount']);
    }
    $creditCardOptions = array(
      'amount' => $amount,
      //contribution page in 4.4 passes currencyID - not sure which passes currency (if any)
      'currency' => !empty($params['currencyID']) ? $params['currencyID'] : $params['currency'],
      'description' => $this->getPaymentDescription($params),
      'transactionId' => isset($params['contributionID']) ? $params['contributionID'] : rand(0, 1000),
      'clientIp' => CRM_Utils_System::ipAddress(),
      'returnUrl' => $this->getReturnSuccessUrl($params['qfKey']),
     // 'cancelUrl' => $this->getCancelUrl($params['qfKey'], CRM_Utils_Array::value('participantID', $params)),
      'card' => $this->getCreditCardObjectParams($params)
    );
    CRM_Utils_Hook::alterPaymentProcessorParams($this, $params, $creditCardOptions);
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
   * @param $params
   * @param string $component
   */
  function doTransferCheckout(&$params, $component = 'contribute') {
    $this->doDirectPayment($params, $component);
  }
}

