<?php
/**
 * @file
 * Created by PhpStorm.
 * User: eileen
 * Date: 17/12/2016
 * Time: 9:47 AM
 */
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
function civicrm_api3_payment_processor_retrymissing($params) {
  $missing = civicrm_api3('PaymentProcessor', 'getmissing', $params);
  $paymentProcessorID = $params['payment_processor_id'];
  foreach ($missing['values'] as $trxn_id => $missingTrxn) {
    try {
      if (isset($missingTrxn['recur_processor_reference'])) {
        if ($missingTrxn['contribution_status_id'] != 'Completed') {
          throw new CRM_Core_Exception('Only completed recurring are supported. 4.7 might do Failed');
        }
        $contributionRecurID = civicrm_api3('ContributionRecur', 'getvalue', array(
          'processor_id' => $missingTrxn['recur_processor_reference'],
          'return' => 'id',
        ));
        $originalContribution = civicrm_api3('Contribution', 'getsingle', array(
          'contribution_recur_id' => $contributionRecurID,
          'options' => array('limit' => 1),
        ));
        $result[$trxn_id]['original_contribution'] = $originalContribution;
        civicrm_api3('Contribution', 'repeattransaction', array(
          'original_contribution_id' => $originalContribution['id'],
          'payment_processor_id' => $paymentProcessorID,
          'receive_date' => $missingTrxn['receive_date'],
          'trxn_id' => $trxn_id,
          'contribution_status_id' => 1,
          'total_amount' => $missingTrxn['total_amount'],
        ));
      }
      else {
        if (empty($params['financial_type_id'])) {
          throw new CRM_Core_Exception('Financial type id is mandatory if not recurring');
        }
        $contactID = civicrm_api3('Contact', 'getvalue', array(
          'first_name' => $missingTrxn['first_name'],
          'email' => $missingTrxn['email'],
          'last_name' => $missingTrxn['last_name'],
          'return' => 'id',
        ));
        civicrm_api3('Contribution', 'create', array(
          'contact_id' => $contactID,
          'trxn_id' => $missingTrxn['trxn_id'],
          'total_amount' => $missingTrxn['total_amount'],
          'receive_date' => $missingTrxn['receive_date'],
          'invoice_id' => $missingTrxn['invoice_id'],
          'contribution_source' => $missingTrxn['contribution_source'],
          'financial_type_id' => $params['financial_type_id'],
          'contribution_status_id' => $params['contribution_status_id'],
          'payment_instrument_id' => 'Credit Card',
        ));
      }
    }
    catch (Exception $e) {
      echo $e->getMessage();
      print_r($missingTrxn);
    }

  }
}
