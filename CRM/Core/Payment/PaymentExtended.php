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

/**
 * Class CRM_Core_PaymentExtended
 * This class holds all the things that really belong in the parent class but we don't want to update into core right now
 */
abstract class CRM_Core_Payment_PaymentExtended extends CRM_Core_Payment {
  /**
   * For code clarity declare is_test as a boolean
   * @var bool
   */
  protected $_is_test = FALSE;

  /**
   * Component - event or contribute
   * @var string
   */
  protected $_component;

  /**
   * Transaction ID is the contribution in the redirect flow and a random number in the on-site->POST flow
   * Ideally the contribution id would always be created at this point in either flow for greater consistency
   * @var
   */
  protected $transaction_id;

  /**
   * Constructor
   *
   * @param string $mode the mode of operation: live or test
   *
   * @param array $paymentProcessor
   *
   */
  function __construct($mode, &$paymentProcessor) {
    $this->_mode = $mode;
    $this->_is_test = ($mode == 'live') ? FALSE : TRUE;
    $this->_paymentProcessor = $paymentProcessor;
    $this->_processorName = !empty($paymentProcessor['payment_processor_type']) ? $paymentProcessor['payment_processor_type'] : $paymentProcessor['name'];
  }

  /**
   * Get base url dependent on component
   * @return string|void
   */
  protected function getBaseReturnUrl() {
    if ($this->_component == 'event') {
      $baseURL = 'civicrm/event/register';
    }
    else {
      $baseURL = 'civicrm/contribute/transact';
    }
    return $baseURL;
  }

  /**
   * Get URL to return the browser to on success
   *
   * @param $qfKey
   *
   * @return string
   */
  protected function getReturnSuccessUrl($qfKey) {
    return CRM_Utils_System::url($this->getBaseReturnUrl(), array(
        '_qf_ThankYou_display' => 1,
        'qfKey' => $qfKey
      ),
      TRUE, NULL, FALSE
    );

  }

  /**
   * Get url to return to after cancelled or failed transaction
   *
   * @param $qfKey
   * @param $participantID
   *
   * @return string cancel url
   */
  protected function getCancelUrl($qfKey, $participantID) {
    if ($this->_component == 'event') {
      return CRM_Utils_System::url($this->getBaseReturnUrl(), array(
          'reset' => 1,
          'cc' => 'fail',
          'participantId' => $participantID,
        ),
        TRUE, NULL, FALSE
      );
    }

    return CRM_Utils_System::url($this->getBaseReturnUrl(), array(
        '_qf_Main_display' => 1,
        'qfKey' => $qfKey,
        'cancel' => 1,
      ),
      TRUE, NULL, FALSE
    );
  }

  /**
   * Get URl for when the back button is pressed
   *
   * @param $qfKey
   *
   * @return string url
   */
  protected function getGoBackUrl($qfKey) {
    return CRM_Utils_System::url($this->getBaseReturnUrl(), array(
        '_qf_Confirm_display' => 'true',
        'qfKey' => $qfKey
      ),
      TRUE, NULL, FALSE
    );
  }

  /**
   * Store the URL for browser redirection in the session for use upon return
   * @param $qfKey
   */
  protected function storeReturnUrls($qfKey) {
    CRM_Core_Session::singleton()->set("ipn_success_url_{$this->transaction_id}", $this->getReturnSuccessUrl($qfKey));
  }

  /**
   * Get URL out of session
   *
   * @param string $type result type
   *  - success
   *
   * @return string url to redirect to
   */
  protected function getStoredUrl($type) {
    return CRM_Core_Session::singleton()->get("ipn_{$type}_url_{$this->transaction_id}");
  }

  /**
   * Get description of payment to pass to processor. This is often what people see in the interface so we want to get
   * as much unique information in as possible within the field length (& presumably the early part of the field)
   *
   * People seeing these can be assumed to be advanced users so quantity of information probably trumps
   * having field names to clarify
   *
   * @param array $params
   * @param int $length
   *
   * @return string
   */
  protected function getPaymentDescription($params, $length = 24) {
    $parts = array('contactID', 'contributionID', 'description', 'billing_first_name', 'billing_last_name');
    $validParts = array();
    if (isset($params['description'])) {
      $uninformativeStrings = array(ts('Online Event Registration: '), ts('Online Contribution: '));
      $params['description'] = str_replace($uninformativeStrings, '', $params['description']);
    }
    foreach ($parts as $part) {
      if ((!empty($params[$part]))) {
        $validParts[] = $params[$part];
      }
    }
    return substr(implode('-', $validParts), 0, $length);
  }

  /**
   * Handle processor error. If we pass error handling through this function it will be easy to switch to throwing exceptions later
   *
   * @param string $level
   * @param $message
   * @param $context
   *
   * @param int $errorCode
   * @param string $userMessage
   *
   * @return mixed
   */
  protected function handleError($level, $message, $context, $errorCode = 9001, $userMessage = NULL) {
    if (omnipaymultiprocessor__versionAtLeast(4.5)) {
      $log = new CRM_Utils_SystemLogger();
      $log->log($level, $message, $context);
    }
    else {
      CRM_Core_Error::debug($errorCode . ': ' . $message . print_r($context, TRUE));
    }
    CRM_Core_Session::setStatus(empty($userMessage) ? $message : $userMessage);
    return new CRM_Core_Error();
  }

  /**
   * get array of fields that should be displayed on the payment form
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function getPaymentFormFields() {
    $billingMode = civicrm_api3('option_value', 'getvalue', array('value' => $this->_paymentProcessor['payment_type'], 'option_group_id' => 'payment_type', 'return' => 'name'));
    $fn = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $billingMode))) . 'FormFields';
    return $this->$fn();
  }

  /**
   * get array of fields that should be displayed on the payment form for credit cards
   * @return array
   */
  protected function getCreditCardFormFields() {
    return array(
      'credit_card_type',
      'credit_card_number',
      'cvv2',
      'credit_card_exp_date',
    );
  }

  /**
   * get array of fields that should be displayed on the payment form for direct debits
   * @return array
   */
  protected function getDirectDebitFormFields() {
    return array(
      'account_holder',
      'bank_account_number',
      'bank_identification_number',
      'bank_name',
    );
  }

  /**
   * get array of fields that should be displayed on the payment form for credit cards using off-site post
   * @return array
   */
  protected function getCreditCardOffSitePostFormFields() {
    return array(
    );
  }

  /**
   * Set transaction id - based on contribution id if exists or else a random string
   * Note that ideally the contribution ID would always be set in all flows but this would require a core change
   * currently it is not set on the on-site form flow - presumably to save creating & deleting them for failed transactions
   * but the case for this seems to pale beside the value of having consistency in approach - & this is dealt with for off-site anyway
   * (although somewhat controversially as failed transactions are deleted which does not suit those who wish to see evidence of what happened
   *
   * @param integer|null $contribution_id Contribution ID
   */
  protected function setTransactionID($contribution_id) {
    if ($contribution_id) {
      $this->transaction_id = $contribution_id;
    }
    else {
      $this->transaction_id = rand(0, 1000);
    }
  }
  /**
   * handle response from processor
   * (this doesn't do anything but by virtue of it existing at least the logger fires :-)
   */
  public function handlePaymentNotification() {
  }
}
