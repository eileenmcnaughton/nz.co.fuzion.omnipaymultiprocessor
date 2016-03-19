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
  $omnipayProcessors = civicrm_api3('PaymentProcessor', 'get', array('class_name' => 'Payment_OmnipayMultiProcessor'));
  $recurringPayments = civicrm_api3('ContributionRecur', 'get', array(
    'next_sched_contribution_date' => 'today',
    'payment_processor_id' => array('IN' => array_keys($omnipayProcessors['values'])),
  ));

  $result = array();
  foreach ($recurringPayments['values'] as $recurringPayment) {
    $paymentProcessorID = $recurringPayment['payment_processor_id'];
    try {
      $originalContribution = civicrm_api3('Contribution', 'getsingle', array(
        'contribution_recur_id' => $recurringPayment['id'],
        'options' => array('limit' => 1),
        'is_test' => CRM_Utils_Array::value('is_test', $recurringPayment['is_test']),
      ));
      $result[$recurringPayment['id']]['original_contribution'] = $originalContribution;
      $pending = civicrm_api3('Contribution', 'repeattransaction', array(
        'original_contribution_id' => $originalContribution['id'],
        'contribution_status_id' => 'Pending',
        'payment_processor_id' => $paymentProcessorID,
      ));

      $payment = civicrm_api3('PaymentProcessor', 'pay', array(
        'amount' => $originalContribution['total_amount'],
        'currency' => $originalContribution['currency'],
        'payment_processor_id' => $paymentProcessorID,
        'contributionID' => $pending['id'],
        'token' => civicrm_api3('PaymentToken', 'getvalue', array(
          'id' => $recurringPayment['payment_token_id'],
          'return' => 'token',
        )),
      ));
      civicrm_api3('Contribution', 'completetransaction', array(
        'id' => $pending['id'],
        'trxn_id' => $payment['trxn_id'],
      ));
      $result['success']['ids'] = $recurringPayment['id'];

    }
    catch (CiviCRM_API3_Exception $e) {
      // Failed - what to do?
      civicrm_api3('ContributionRecur', 'create', array(
        'id' => $recurringPayment['id'],
        'failure_count' => $recurringPayment['failure_count'] + 1,
      ));
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
