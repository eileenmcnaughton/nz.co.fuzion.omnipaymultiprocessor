<?php

/**
 * Query Payment Processor recurring details.
 *
 * @param array $params
 *
 * @throws API_Exception
 * @throws CiviCRM_API3_Exception
 *
 * @return array
 *   API Result array
 */
function civicrm_api3_payment_processor_query($params) {
  $processor = civicrm_api3('payment_processor', 'getsingle', array('id' => $params['payment_processor_id']));
  $responder = new CRM_Core_Payment_OmnipayMultiProcessor(($processor['is_test'] ? 'test' : 'live'), $processor);
  $gatewayParams = array();
  if (!empty($params['start_date_time'])) {
    $gatewayParams['startTimestamp'] = strtotime($params['start_date_time']);
  }
  if (!empty($params['end_date_time'])) {
    $gatewayParams['endTimestamp'] = strtotime($params['end_date_time']);
  }
  $result = $responder->query($gatewayParams);
  $payments = array();
  foreach ($result as $id => /*@var \Omnipay\Common\Message\QueryDetailResponse $response*/ $response) {
    $payment = array(
      'first_name' => $response->getFirstName(),
      'last_name' => $response->getLastName(),
      'invoice_id' => $response->getTransactionId(),
      'trxn_id' => $response->getTransactionReference(),
      'street_address' => $response->getBillingAddress1(),
      'postal_code' => $response->getBillingPostcode(),
      'state_province_id' => $response->getBillingState(),
      'country_id' => $response->getBillingCountry(),
      'city' => $response->getBillingCity(),
      'total_amount' => $response->getAmount(),
      'email' => $response->getEmail(),
      'contribution_source' => $response->getDescription(),
      'receive_date' => date('Y-m-d H:i:s', strtotime($response->getTransactionDate())),
      'settled_date' => date('Y-m-d H:i:s', strtotime($response->getSettlementDate())),
    );
    if ($response->isSuccessful()) {
      $payment['contribution_status_id'] = 'Completed';
    }
    elseif ($response->getResultCode() == 2) {
      $payment['contribution_status_id'] = 'Failed';
    }
    if ($response->isRecurring()) {
      $payment['gateway_contact_reference'] = $response->getCustomerReference();
      $payment['recur_processor_reference'] = $response->getRecurringReference();
    }
    $payments[$payment['trxn_id']] = $payment;
  }

  return civicrm_api3_create_success($payments, $params);
}

/**
 * Define metadata for payment_processor.tokenquery.
 *
 * @param array $params
 */
function _civicrm_api3_payment_processor_query_spec(&$params) {
  $params['contribution_recur_id'] = array(
    'title' => 'Contribution Recur ID',
    'type' => CRM_Utils_Type::T_INT,
  );
  $params['is_recur'] = array(
    'title' => 'Is recurring?',
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
}
