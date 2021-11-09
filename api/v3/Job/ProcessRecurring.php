<?php

/**
 * Pass all due recurring contributions to the processor to action (if possible).
 *
 * @param array $params
 *
 * @return array
 *   API result array.
 * @throws CiviCRM_API3_Exception
 */
function civicrm_api3_job_process_recurring($params) {
  $omnipayProcessors = civicrm_api3('PaymentProcessor', 'get', ['class_name' => 'Payment_OmnipayMultiProcessor', 'domain_id' => CRM_Core_Config::domainID()]);
  $recurringPayments = civicrm_api3('ContributionRecur', 'get', [
    'next_sched_contribution_date' => ['BETWEEN' => [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]],
    'payment_processor_id' => ['IN' => array_keys($omnipayProcessors['values'])],
    'contribution_status_id' => ['IN' => ['In Progress', 'Pending', 'Overdue']],
    'options' => ['limit' => 0],
  ]);

  $result = [];
  foreach ($recurringPayments['values'] as $recurringPayment) {
    $paymentProcessorID = $recurringPayment['payment_processor_id'];
    try {
      $originalContribution = civicrm_api3('Contribution', 'getsingle', [
        'contribution_recur_id' => $recurringPayment['id'],
        'options' => ['limit' => 1],
        'is_test' => CRM_Utils_Array::value('is_test', $recurringPayment['is_test']),
        'contribution_test' => CRM_Utils_Array::value('is_test', $recurringPayment['is_test']),
      ]);
      $result[$recurringPayment['id']]['original_contribution'] = $originalContribution;
      $pending = civicrm_api3('Contribution', 'repeattransaction', [
        'original_contribution_id' => $originalContribution['id'],
        'total_amount' => $recurringPayment['amount'],
        'contribution_status_id' => 'Pending',
        'payment_processor_id' => $paymentProcessorID,
        'is_email_receipt' => FALSE,
      ]);

      $payment = civicrm_api3('PaymentProcessor', 'pay', [
        'amount' => $recurringPayment['amount'],
        'currency' => $originalContribution['currency'],
        'payment_processor_id' => $paymentProcessorID,
        'contributionID' => $pending['id'],
        'contribution_id' => $pending['id'],
        'contactID' => $originalContribution['contact_id'],
        'description' => ts('Repeat payment, original was ' . $originalContribution['id']),
        'token' => civicrm_api3('PaymentToken', 'getvalue', [
          'id' => $recurringPayment['payment_token_id'],
          'return' => 'token',
        ]),
        'payment_action' => 'purchase',
      ]);
      $payment = reset($payment['values']);

      civicrm_api3('Contribution', 'completetransaction', [
        'id' => $pending['id'],
        'trxn_id' => $payment['trxn_id'],
        'payment_processor_id' => $paymentProcessorID,
      ]);
      $result['success']['ids'] = $recurringPayment['id'];

    }
    catch (CiviCRM_API3_Exception $e) {
      // Failed - what to do?
      civicrm_api3('ContributionRecur', 'create', [
        'id' => $recurringPayment['id'],
        'failure_count' => $recurringPayment['failure_count'] + 1,
      ]);
      if (!empty($pending['id'])) {
        civicrm_api3('Contribution', 'create', [
          'id' => $pending['id'],
          'contribution_status_id' => 'Failed',
          'debug' => $params['debug'] ?? 0,
        ]);
      }
      $result[$recurringPayment['id']]['error'] = $e->getMessage();
      $result['failed']['ids'] = $recurringPayment['id'];
    }
  }
  return civicrm_api3_create_success($result, $params);
}

/**
 * Action Payment.
 *
 * @param array $params
 *
 * @return array
 */
function _civicrm_api3_job_process_recurring_spec(&$params) {
}
