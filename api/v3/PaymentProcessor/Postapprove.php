<?php
/**
 * Action payment.
 *
 * @param array $params
 *
 * @return array
 *   API result array.
 * @throws CRM_Core_Exception
 */
function civicrm_api3_payment_processor_postapprove($params) {
  $processor = Civi\Payment\System::singleton()->getById($params['payment_processor_id']);
  $processor->setPaymentProcessor(civicrm_api3('PaymentProcessor', 'getsingle', array('id' => $params['payment_processor_id'])));
  $result = $processor->doPostApproval($params);
  if (is_a($result, 'CRM_Core_Error')) {
    throw CRM_Core_Exception('Payment failed');
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
function _civicrm_api3_payment_processor_postapprove_spec(&$params) {
  $params['payment_processor_id']['api.required'] = 1;
  $params['amount']['api.required'] = 1;
  $params['component']['api.default'] = 'contribute';
  $params['component']['api.required'] = 'currency';
  $params['is_recur']['api.default'] = 0;
}
