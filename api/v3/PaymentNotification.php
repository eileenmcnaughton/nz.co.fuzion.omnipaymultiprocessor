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
    $params = json_decode(civicrm_api3('system_log', 'getvalue', array('id' => $params['system_log_id'], 'return' => 'context')), TRUE);
  }
  return civicrm_api3_create_success(CRM_Core_Payment_OmnipayMultiProcessor::processPaymentResponse($params));
}
