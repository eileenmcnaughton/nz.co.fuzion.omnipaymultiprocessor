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
    $this->_processorName = $paymentProcessor['payment_processor_type'];
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
      if ($userMessage) {
        $message = $userMessage;
      }
      CRM_Core_Session::setStatus($message);
    }
    else {
      CRM_Core_Session::setStatus($message . $userMessage);
      CRM_Core_Error::debug(!empty($userMessage) ? $userMessage : $message);
    }
  }
}
