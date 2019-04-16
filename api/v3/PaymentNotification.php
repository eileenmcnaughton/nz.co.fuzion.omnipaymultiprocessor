<?php

/**
 * Process incoming payment notification (we have called it 'create' so that it will be forced to be transactional.
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_payment_notification_create($params) {
  if (!empty($params['system_log_id'])) {
    // lets replace params with this rather than allow altering
    $log = civicrm_api3('system_log', 'getsingle', array('id' => $params['system_log_id'], 'return' => 'context, message'));
    $params = json_decode($log['context'], true);
  }
  if (empty($params['processor_id']) && stristr($log['message'], 'processor_id=')) {
    $parts = explode('processor_id=', $log['message']);
    $params['processor_id'] = array_pop($parts);
  }
  return civicrm_api3_create_success(CRM_Core_Payment_OmnipayMultiProcessor::processPaymentResponse($params));
}
