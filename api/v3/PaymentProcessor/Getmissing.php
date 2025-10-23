<?php

/**
 * Query Payment Processor recurring details.
 *
 * @param array $params
 *
 * @throws CRM_Core_Exception
 * @throws CRM_Core_Exception
 *
 * @return array
 *   API Result array
 */
function civicrm_api3_payment_processor_getmissing($params) {
  $lazyLogging = $params['lazy_logging'];
  unset($params['lazy_logging']);
  $result = civicrm_api3('PaymentProcessor', 'query', $params);
  $params['lazy_logging']  = $lazyLogging;
  $missing = array();
  $contributionStatuses = civicrm_api3('Contribution', 'getoptions', array('field' => 'contribution_status_id'));
  $contributionStatusFilter = empty($params['contribution_status_id']) ? NULL : $contributionStatuses['values'][$params['contribution_status_id']];
  $first = TRUE;
  foreach ($result['values'] as $payment) {
    if ($contributionStatusFilter && $payment['contribution_status_id'] != $contributionStatusFilter) {
      continue;
    }
    if (isset($params['is_recur'])) {
      if ($params['is_recur'] && empty($payment['recur_processor_reference'])) {
        continue;
      }
      if (($params['is_recur'] === FALSE || $params['is_recur'] === 0) && !empty($payment['recur_processor_reference'])) {
        continue;
      }
    }
    try {
      civicrm_api3('Contribution', 'getsingle', array('trxn_id' => $payment['trxn_id']));
    }
    catch (Exception $e) {
      $missing[$payment['trxn_id']] = $payment;
      if (!empty($params['lazy_logging'])) {
        $payment['payment_processor_id'] = $params['payment_processor_id'];
        if ($first) {
          echo implode('|', array_keys($payment)) . "\n";
          $first = FALSE;
        }
        echo implode('|', $payment) . "\n";
      }
    }
  }
  if (empty($params['lazy_logging'])) {
    return civicrm_api3_create_success($missing, $params);
  }
}

/**
 * Define metadata for payment_processor.tokenquery.
 *
 * @param array $params
 */
function _civicrm_api3_payment_processor_getmissing_spec(&$params) {
  $params['contribution_recur_id'] = array(
    'title' => 'Contribution Recur ID',
    'type' => CRM_Utils_Type::T_INT,
  );
  $params['contribution_status_id'] = array(
    'title' => 'Contribution Status ID',
    'type' => CRM_Utils_Type::T_INT,
    'pseudoconstant' => array(
      'optionGroupName' => 'contributionStatus',
      'keyColumn' => 'name',
    ),
  );
  $params['is_recur'] = array(
    'title' => 'Is recurring?',
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
}
