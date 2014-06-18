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
  static function singleton($mode = 'test', &$paymentProcessor, &$paymentForm = NULL, $force = FALSE) {
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
    //$this->_is_test = TRUE;
    $this->_component = strtolower($component);

    $gateway = Omnipay::create(str_replace('Ominpay_', '', $this->_paymentProcessor['payment_processor_type']));
    $gateway->setUsername($this->_paymentProcessor['user_name']);
    $gateway->setPassword($this->_paymentProcessor['password']);
    $gateway->setTestMode($this->_is_test);

    if(method_exists($gateway, 'setSignature')) {
      //so far paypal supports & payment express doesn't - will we need a wrapper for maybe supported functions?
      $gateway->setSignature($this->_paymentProcessor['signature']);
    }

    //@todo - interrogate processors
    //$settings = $gateway->getDefaultParameters();

    try {
      $response = $gateway->purchase($this->getCreditCardOptions($params, $component))->send();
      if ($response->isSuccessful()) {
        // mark order as complete
        $params['trxn_id'] = $response->getTransactionReference();
        //gross_amount ? fee_amount?
        return $params;
      }
      elseif ($response->isRedirect()) {
        //ie off site processor
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
      'billingCity' => 'city-' . $billingID,
      'billingPostcode' => 'billing_postal_code-' . $billingID,
      'billingState' => 'billing_state_province_id-' . $billingID,
      'billingCountry' => 'billing_country_id-' . $billingID,
      'billingPhone' => 'phone', // we don't specifically anticipate phone to come through - adding this & company as 'best guess'
      'company' => 'organization_name',
      'type' => 'credit_card_type',
    );

    foreach ($basicMappings as $cardField => $civicrmField) {
      $cardFields[$cardField] = isset($params[$civicrmField]) ? $params[$civicrmField] : '';
    }

    if(is_numeric($cardFields['billingCountry'])) {
      $cardFields['billingCountry'] = CRM_Core_PseudoConstant::countryIsoCode($cardFields['billingCountry']);
    }
    if(is_numeric($cardFields['billingCountry'])) {
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
    $creditCardOptions = array(
      'amount' => (float) CRM_Utils_Rule::cleanMoney($params['total_amount']),
      'currency' => $params['currency'],
      'description' => $this->getPaymentDescription($params),
      'transactionId' => isset($params['contributionID']) ? $params['contributionID'] : '',
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
    //@todo check gateway against $gateway->getDefaultParameters();
  }

  /**
   * @param $params
   * @param string $component
   */
  function doTransferCheckout(&$params, $component = 'contribute') {
    $this->doDirectPayment($params, $component);
  }
}

