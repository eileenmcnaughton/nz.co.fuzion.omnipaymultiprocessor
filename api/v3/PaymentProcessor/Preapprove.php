<?php
/**
 * Action payment.
 *
 * @param array $params
 *
 * @return array
 *   API result array.
 * @throws CiviCRM_API3_Exception
 */
function civicrm_api3_payment_processor_preapprove($params) {
  $processor = Civi\Payment\System::singleton()->getById($params['payment_processor_id']);
  $processor->setPaymentProcessor(civicrm_api3('PaymentProcessor', 'getsingle', array('id' => $params['payment_processor_id'])));
  $result = $processor->doPreApproval($params);
  if (is_a($result, 'CRM_Core_Error')) {
    throw API_Exception('Payment failed');
  }
  return civicrm_api3_create_success(array($result['pre_approval_parameters']), $params);
}

/**
 * Action payment.
 *
 * @param array $params
 *
 * @return array
 */
function _civicrm_api3_payment_processor_preapprove_spec(&$params) {
  $params['payment_processor_id']['api.required'] = 1;
  $params['amount']['api.required'] = 1;
}
