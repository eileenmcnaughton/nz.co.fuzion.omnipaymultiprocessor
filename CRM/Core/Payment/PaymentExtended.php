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
   * Transaction id (contribution id)  with any transformations applied.
   *
   * @var string
   */
  protected $formatted_transaction_id;

  /**
   * @var string label for payemnt field set.
   */
  protected $payment_type_label;

  /**
   * @var \Omnipay\Common\AbstractGateway
   */
  protected $gateway;

  /**
   * @var CRM_Utils_SystemLogger;
   */
  protected $log;

  /**
   * @return \CRM_Utils_SystemLogger
   */
  public function getLog() {
    if (!$this->log) {
      $this->setLog(new CRM_Utils_SystemLogger());
    }
    return $this->log;
  }

  /**
   * @param \CRM_Utils_SystemLogger $log
   */
  public function setLog($log) {
    $this->log = $log;
  }

  /**
   * Class Constructor.
   *
   * @param string $mode the mode of operation: live or test
   *
   * @param array $paymentProcessor
   */
  public function __construct($mode, &$paymentProcessor) {
    $this->_mode = $mode;
    $this->_is_test = ($mode == 'live') ? FALSE : TRUE;
    $this->_paymentProcessor = $paymentProcessor;
  }

  /**
   * Get URL to return the browser to on success.
   *
   * @param string $qfKey
   *
   * @return string
   * @throws \CRM_Core_Exception
   */
  protected function getReturnSuccessUrl($qfKey = NULL) {
    if (isset($this->successUrl)) {
      $returnURL = $this->successUrl;
    }
    else {
      $wfNode = CRM_Utils_Request::retrieve('wfNode', 'String');
      $sid = CRM_Utils_Request::retrieve('sid', 'Integer');
      $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String');

      if ($wfNode) {
        // Drupal webform
        $returnURL = CRM_Utils_System::url("{$wfNode}/done", ['sid' => $sid], TRUE, NULL, FALSE, TRUE);
      }
      else {
        // Contribution / event
        $returnURL = CRM_Utils_System::url($this->getBaseReturnUrl(), [
          '_qf_ThankYou_display' => 1,
          'qfKey' => $qfKey
        ], TRUE, NULL, FALSE, TRUE);
      }
    }

    Civi::log()->debug('getReturnSuccessUrl: ' . $returnURL);
    return $returnURL;
  }

  /**
   * @param string $key
   * @param int $participantID
   * @param int $eventID
   *
   * @return string
   * @throws \CRM_Core_Exception
   */
  protected function getReturnFailUrl($key, $participantID = NULL, $eventID = NULL) {
    if (isset($this->cancelUrl)) {
      $returnURL = $this->cancelUrl;
    }
    else {
      $wfNode = CRM_Utils_Request::retrieve('wfNode', 'String');
      $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String');
      $url = ($this->_component == 'event') ? 'civicrm/event/register' : 'civicrm/contribute/transact';
      $cancel = ($this->_component == 'event') ? '_qf_Register_display' : '_qf_Main_display';

      // Default cancel
      $returnURL = CRM_Utils_System::url($url, [
        $cancel => 1,
        'cancel' => 1,
        'qfKey' => $qfKey
      ], TRUE, NULL, FALSE, TRUE);
      if ($wfNode) {
        $returnURL = CRM_Utils_System::url("{$wfNode}", NULL, TRUE, NULL, FALSE, TRUE);
      }
    }
    Civi::log()->debug('getReturnFailUrl: ' . $returnURL);
    return $returnURL;
  }

  /**
   * Get the notify (aka ipn, web hook or silent post) url.
   *
   * Note we do not pass the 'mode' for test as this breaks some processors.
   * People wanting to use test mode need a version of CiviCRM with
   * a patch for CRM-15978.
   *
   * @param bool $allowLocalHost
   *   When True f there is no '.' in it we assume that we are dealing with localhost or
   *   similar and it is unreachable from the web & hence invalid.
   *
   * @return string
   *    URL to notify outcome of transaction.
   */
  protected function getNotifyUrl($allowLocalHost = FALSE) {
    // For redirect offsite payment processors we need either the qfKey or 'q' (for webform)
    // to rebuild the redirect URL
    // These are then passed back via getReturnSuccess/FailURL to the payment processor so we can redirect to the right ending page
    $wfNode = CRM_Utils_Array::value('q', $_GET);
    $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String');
    $query = NULL;
    if ($qfKey) {
      // If we have a qfKey we are not a webform
      $query['qfKey'] = $qfKey;
    }
    elseif ($wfNode) {
      // Otherwise assume we are.
      $query['wfNode'] = $wfNode;
    }

    $url = CRM_Utils_System::url(
      'civicrm/payment/ipn/' . $this->formatted_transaction_id . '/' . $this->_paymentProcessor['id'],
      $query,
      TRUE,
      NULL,
      FALSE
    );
    Civi::log()->debug('getNotifyUrl: ' . $url);
    return $allowLocalHost ? $url : ((stristr($url, '.')) ? $url : '');
  }

  /**
   * Store the URL for browser redirection in the session for use upon return.
   *
   * @param string $qfKey
   * @param int $participantID
   * @param int $eventID
   */
  protected function storeReturnUrls($qfKey, $participantID = NULL, $eventID = NULL) {
    CRM_Core_Session::singleton()->set("ipn_success_url_{$this->transaction_id}", $this->getReturnSuccessUrl($qfKey));
    CRM_Core_Session::singleton()->set("ipn_fail_url_{$this->transaction_id}", $this->getReturnFailUrl($qfKey, $participantID, $eventID));
  }

  /**
   * Store the data required on the payment form.
   *
   * @param string $key
   * @param array $data
   */
  protected function storeTransparentRedirectFormData($key, $data) {
    CRM_Core_Session::singleton()->set("transparent_redirect_data" . $key, json_encode($data));
  }

  /**
   * Get URL out of session.
   *
   * @param string $type result type
   *  - success
   *
   * @return string
   *   Url to redirect to
   */
  protected function getStoredUrl($type) {
    return CRM_Core_Session::singleton()->get("ipn_{$type}_url_{$this->transaction_id}");
  }

  /**
   * Get description of payment to pass to processor.
   *
   * This is often what people see in the interface so we want to get
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
    if (empty($validParts) && !empty($params['is_recur'])) {
      $validParts[] = ts('regular payment');
    }
    return substr(implode('-', $validParts), 0, $length);
  }

  /**
   * Get label for the payment information type.
   *
   * @return string
   */
  public function getPaymentTypeLabel() {
    if (!isset($this->payment_type_label)) {
      $this->payment_type_label = civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'payment_type',
        'return' => 'label',
        'value' => $this->_paymentProcessor['payment_type'],
      ));
    }
    return $this->payment_type_label;
  }

  /**
   * Handle processor error.
   *
   * If we pass error handling through this function it will be easy to switch to throwing exceptions later.
   *
   * @param string $level
   * @param string $message
   * @param string $context
   *
   * @param int $errorCode
   * @param string $userMessage
   *
   * @return mixed
   * @throws \Civi\Payment\Exception\PaymentProcessorException
   */
  protected function handleError($level, $message, $context, $errorCode = 9001, $userMessage = NULL) {
    // Reset gateway to NULL so don't cache it. The size of the object or closures within it could
    // cause problems when serializing & saving.
    $this->gateway = NULL;
    Civi::log()->log($level, $message, (array) $context);
    $log = new CRM_Utils_SystemLogger();
    $log->log($level, $message, (array) $context);

    if (CRM_Core_Permission::check('administer payment processors')) {
      $userMessage = implode(',', $context);
    }
    throw new \Civi\Payment\Exception\PaymentProcessorException($userMessage);
  }

  /**
   * Get array of fields that should be displayed on the payment form.
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function getPaymentFormFields() {
    $paymentType = civicrm_api3('option_value', 'getvalue', array('value' => $this->_paymentProcessor['payment_type'], 'option_group_id' => 'payment_type', 'return' => 'name'));
    $fn = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $paymentType))) . 'FormFields';
    if ($fn == 'getCreditCardFormFields' && $this->_paymentProcessor['billing_mode'] == 4) {
      //@todo this is a traditional off-site processor
      return array();
    }
    return $this->$fn();
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
  protected function setTransactionID($contribution_id, $prefixAction = 'add') {
    $prefix = $this->getPrefix();
    if ($contribution_id) {
      if ($prefixAction === 'strip') {
        $this->transaction_id = substr($contribution_id, strlen($prefix));
      }
      else {
        $this->transaction_id = $contribution_id;
      }
    }
    else {
      // When does this miserable occurence still happen.
      // need to fix if it does!
      $this->transaction_id = rand(0, 1000);
    }
    $this->formatted_transaction_id = $prefix . $this->transaction_id;
  }
  /**
   * handle response from processor
   * (this doesn't do anything but by virtue of it existing at least the logger fires :-)
   */
  public function handlePaymentNotification() {
  }

  protected function getPrefix() {
    $paymentFields = civicrm_api3('PaymentProcessor', 'getfields', []);
    foreach ($paymentFields['values'] as $paymentField) {
      if (substr($paymentField['name'], 0, 7) === 'custom_'
        && ts($paymentField['title']) === ts('Transaction Prefix')) {
          $processor = civicrm_api3('PaymentProcessor',
            'getsingle', ['id' => $this->_paymentProcessor['id']]
          );
          return CRM_Utils_Array::value($paymentField['name'], $processor);
        }
    }
  }
}
