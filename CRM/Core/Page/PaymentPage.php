<?php
/**
 * Created by PhpStorm.
 * User: eileen
 * Date: 23/07/2014
 * Time: 6:02 PM
 */
class CRM_Core_Page_PaymentPage extends CRM_Core_Page {


  function run(){
    CRM_Utils_System::setTitle(ts('Enter your payment details'));
    $formData =  $this->getTransparentRedirectFormData(CRM_Utils_Request::retrieve('key', 'String', CRM_Core_DAO::$_nullObject, TRUE));
    $paymentProcessorID = $formData['payment_processor_id'];
    $paymentProcessor = civicrm_api3('payment_processor', 'getsingle', array('id' => $paymentProcessorID));
    $processor = CRM_Core_Payment::singleton('contribute', $paymentProcessor);
    $displayFields = $processor->getTransparentDirectDisplayFields();
    $this->assign('hidden_fields', array_diff_key($formData, $displayFields));
    $this->assign('display_fields', $displayFields);
    $this->assign('post_url', $formData['post_submit_url']);
    return parent::run();
  }

  /**
   * get the data required on the payment form.
   * @param string $key
   * @return array
   */
  protected function getTransparentRedirectFormData($key) {
    return json_decode(CRM_Core_Session::singleton()->get("transparent_redirect_data" . $key), TRUE);
  }

}
