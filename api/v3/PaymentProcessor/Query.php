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
  $responder = new CRM_Core_Payment_OmnipayMultiProcessor('live', $processor);
  $result = $responder->query($params);
  $count = 0;
  foreach ($result as $id => $row) {
    try {
      civicrm_api3('ContributionRecur', 'getsingle', array('processor_id' => $row['id'],
      ));
    }
    catch (Exception $e) {
      $count++;
      echo $count . " " . ($id+1) . " " . $row['id'] . " " . $row['firstName'] . " " . $row['lastName'] . "\n";
    }
  }

  //return civicrm_api3_create_success($result, $params);
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
}
