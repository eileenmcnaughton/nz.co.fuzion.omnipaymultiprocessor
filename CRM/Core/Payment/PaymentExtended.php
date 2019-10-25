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

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

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
   * Array of http requests and responses when in developer mode.
   *
   * @var array
   */
  protected $history = [];

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
   */
  protected function getReturnSuccessUrl($qfKey) {
    if (isset($this->successUrl)) {
      return $this->successUrl;
    }
    return CRM_Utils_System::url($this->getBaseReturnUrl(), array(
        '_qf_ThankYou_display' => 1,
        'qfKey' => $qfKey,
      ),
      TRUE, NULL, FALSE, TRUE
    );
  }

  /**
   * @param $key
   * @param null $participantID
   * @param null $eventID
   * @return string
   */
  protected function getReturnFailUrl($key, $participantID = NULL, $eventID = NULL) {
    if (isset($this->cancelUrl)) {
      return $this->cancelUrl;
    }
    $test =  $this->_is_test ? '&action=preview' : '';
    if ($this->_component == "event") {
      return CRM_Utils_System::url('civicrm/event/register',
        "reset=1&cc=fail&participantId={$participantID}&id={$eventID}{$test}&qfKey={$key}",
        TRUE, NULL, FALSE, TRUE
      );
    }
    else {
      return CRM_Utils_System::url('civicrm/contribute/transact',
        "_qf_Main_display=1&cancel=1&qfKey={$key}{$test}",
        TRUE, NULL, FALSE, TRUE
      );
    }
  }

  /**
   * Get the notify (aka ipn, web hook or silent post) url.
   *
   * Note we do not pass the 'mode' for test as this breaks some processors.
   * People wanting to use test mode need a version of CiviCRM with
   * a patch for CRM-15978.
   *
   * @param bool $allowLocalHost
   *   When True if there is no '.' in it we assume that we are dealing with localhost or
   *   similar and it is unreachable from the web & hence invalid.
   *
   * @return string
   *    URL to notify outcome of transaction.
   */
  protected function getNotifyUrl($allowLocalHost = FALSE) {
    $url = CRM_Utils_System::url(
      'civicrm/payment/ipn/' . $this->formatted_transaction_id . '/' . $this->_paymentProcessor['id'],
      NULL,
      TRUE,
      NULL,
      FALSE
    );
    return $allowLocalHost ? $url : ((stristr($url, '.')) ? $url : '');
  }

  /**
   * Store the URL for browser redirection in the session for use upon return.
   *
   * @param int $participantID
   * @param int $eventID
   */
  protected function storeReturnUrls($participantID = NULL, $eventID = NULL) {
    CRM_Core_Session::singleton()->set("ipn_success_url_{$this->transaction_id}", $this->getReturnSuccessUrl($this->getQfKey()));
    CRM_Core_Session::singleton()->set("ipn_fail_url_{$this->transaction_id}", $this->getReturnFailUrl($this->getQfKey(), $participantID, $eventID));
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
      $validParts[] = E::ts('Regular payment');
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
      $userMessage .= implode(',', $context);
    }
    $this->logHttpTraffic();
    throw new \Civi\Payment\Exception\PaymentProcessorException($userMessage);
  }

  /**
   * Log http traffic for analysis - only in developer mode!
   */
  protected function logHttpTraffic() {
    if (!\Civi::settings()->get('omnipay_developer_mode')) {
      return;
    }
    foreach ($this->history as $transaction) {
      $this->getLog()->debug('omnipay_http_request', [
        'request' => (string) $transaction['request']->getBody(),
        'method' => $transaction['request']->getMethod(),
        'url' => $transaction['request']->getRequestTarget(),
        'headers' => $transaction['request']->getHeaders(),
      ]);
      $this->getLog()->debug('omnipay_http_response', [
        'response' => (string) $transaction['response']->getBody(),
      ]);
    }
    $this->cleanupClassForSerialization();
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
  protected function setContributionReference($contribution_id, $prefixAction = 'add') {
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
      //$this->transaction_id = rand(0, 1000);
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


  /**
   * Implement http://php.net/manual/en/class.serializable.php
   *
   * Removes unserializable elements when the class is serialised.
   *
   * @return string
   */
  public function serialize() {
    $this->cleanupClassForSerialization(TRUE);
    return serialize($this);
  }

  /**
   * Unset various objects that will fail to serialize when the form is stored to session.
   *
   * @param bool $isIncludeGateWay
   *   Should we also unset the gateway.
   *   (possibly the default here should be TRUE but we want to be sure we are not
   *   unsetting it when it is still being used.)
   */
  protected function cleanupClassForSerialization($isIncludeGateWay = FALSE) {
    $this->history = [];
    $this->client = NULL;
    $this->guzzleClient = NULL;
    if ($isIncludeGateWay) {
      $this->gateway = NULL;
    }
  }
}
