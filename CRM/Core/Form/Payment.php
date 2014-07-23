<?php
/**
 * Created by PhpStorm.
 * User: eileen
 * Date: 23/07/2014
 * Time: 6:02 PM
 */
class CRM_Core_Form_Payment extends CRM_Core_Form {


  function buildForm(){
    $paymentProcessorID = CRM_Utils_Request::retrieve('payment_processor_id', 'Integer', CRM_Core_DAO::$_nullObject, TRUE);

    $paymentProcessor = civicrm_api3('payment_processor', 'getsingle', array('id' => $paymentProcessorID));
    $processor = CRM_Core_Payment::singleton('contribute', $paymentProcessor);
    $displayFields = $processor->getTransparentDirectDisplayFields();
    $this->assign('hidden_fields', array_diff_key($_GET, $displayFields));
    $this->assign('display_fields', array_keys($displayFields));
    $this->assign('post_url', CRM_Utils_Request::retrieve('post_submit_url', 'String', CRM_Core_DAO::$_nullObject, TRUE));

    foreach ($displayFields as $fieldName => $field) {
      $this->add(
        $field['htmlType'],
        $fieldName,
        $field['title'],
        $field['attributes'],
        $field['is_required']
      );
    }
    $this->addButtons(
      array(
        array (
          'type'      => 'submit',
          'name'      => ts('Submit'),
          'spacing'   => '',
          'isDefault' => TRUE,
        ),
      )
    );
    return parent::buildForm();
  }

}
