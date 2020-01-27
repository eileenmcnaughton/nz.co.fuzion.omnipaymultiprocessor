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
  $recurringPayments = civicrm_api3('ContributionRecur', 'get', array(
    'next_sched_contribution_date' => ['BETWEEN' => [date('Y-m-d 00:00:00'), date('Y-m-d 12:59:59')]],
    'payment_processor_id' => array('IN' => array_keys($omnipayProcessors['values'])),
    'contribution_status_id' => array('IN' => array('In Progress', 'Pending', 'Overdue')),
    'options' => array('limit' => 0),
  ));

  $result = array();
  foreach ($recurringPayments['values'] as $recurringPayment) {
    $paymentProcessorID = $recurringPayment['payment_processor_id'];
    try {
      $originalContribution = civicrm_api3('Contribution', 'getsingle', array(
        'contribution_recur_id' => $recurringPayment['id'],
        'options' => array('limit' => 1),
        'is_test' => CRM_Utils_Array::value('is_test', $recurringPayment['is_test']),
        'contribution_test' => CRM_Utils_Array::value('is_test', $recurringPayment['is_test']),
      ));
      $result[$recurringPayment['id']]['original_contribution'] = $originalContribution;
      $pending = civicrm_api3('Contribution', 'repeattransaction', array(
        'original_contribution_id' => $originalContribution['id'],
        'contribution_status_id' => 'Pending',
        'payment_processor_id' => $paymentProcessorID,
        'is_email_receipt' => FALSE,
      ));

      if (!is_continuous_authority($omnipayProcessors['values'][$paymentProcessorID])) {
        $payment = civicrm_api3('PaymentProcessor', 'pay', array(
          'amount' => $originalContribution['total_amount'],
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
        ));
        $payment = reset($payment['values']);

        civicrm_api3('Contribution', 'completetransaction', array(
          'id' => $pending['id'],
          'trxn_id' => $payment['trxn_id'],
          'payment_processor_id' => $paymentProcessorID,
        ));
        $result['success']['ids'] = $recurringPayment['id'];
      } else {
        $payment = civicrm_api3('PaymentProcessor', 'pay', array(
          'amount' => $originalContribution['total_amount'],
          'currency' => $originalContribution['currency'],
          'payment_processor_id' => $paymentProcessorID,
          'contributionID' => $pending['id'],
          'contribution_id' => $pending['id'],
          'original_contribution_trxn_id' => $originalContribution['trxn_id'],
          'continuous_authority_repeat' => TRUE,
          'contactID' => $originalContribution['contact_id'],
          'description' => ts('Repeat payment, original was ' . $originalContribution['id']),
          'payment_action' => 'purchase',
        ));
        $payment = reset($payment['values']);

        civicrm_api3('Contribution', 'completetransaction', array(
          'id' => $pending['id'],
          'trxn_id' => $payment['trxn_id'],
          'payment_processor_id' => $paymentProcessorID,
        ));
        $result['success']['ids'] = $recurringPayment['id'];
      }
    }
    catch (CiviCRM_API3_Exception $e) {
      // Failed - what to do?
      civicrm_api3('ContributionRecur', 'create', array(
        'id' => $recurringPayment['id'],
        'failure_count' => $recurringPayment['failure_count'] + 1,
      ));
      civicrm_api3('Contribution', 'create', array(
        'id' => $pending['id'], 'contribution_status_id' => 'Failed', 'debug' => $params['debug'])
      );
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

function is_continuous_authority($paymentProcessor) {
  $paymentProcessorType = civicrm_api3('PaymentProcessorType', 'get', array(
    'id' => $paymentProcessor['payment_processor_type_id'],
    'sequential' => 1,
  ));

  $property = get_processor_type_property($paymentProcessorType['values'][0]['name'], 'continuous_authority');

  if($property['found']) {
    return $property['value'];
  } else {
    return FALSE;
  }
}

function get_processor_type_by_name($processorTypeName, $entity) {
  if($entity['entity'] != 'payment_processor_type') { return FALSE; }
  if(!array_key_exists('params', $entity)) { return FALSE; }
  if(!array_key_exists('name', $entity['params'])) { return FALSE; }
  return $entity['params']['name'] == $processorTypeName;
}

function get_processor_type_metadata($processorTypeName) {
  $entities = [];
  omnipaymultiprocessor_civicrm_managed($entities);
  $filter_by_name = function($entity) use($processorTypeName) {
    return get_processor_type_by_name($processorTypeName, $entity);
  };
  return array_values(array_filter($entities, $filter_by_name))[0];
}

function get_processor_type_property($processorTypeName, $propertyName) {
  $processorTypeMetadata = get_processor_type_metadata($processorTypeName);
  if(!array_key_exists('metadata', $processorTypeMetadata)) { return array('found' => FALSE); }
  if(!array_key_exists($propertyName, $processorTypeMetadata['metadata'])) { return array('found' => FALSE); }
  return array(
    'found' => TRUE,
    'value' => $processorTypeMetadata['metadata'][$propertyName],
  );
}
