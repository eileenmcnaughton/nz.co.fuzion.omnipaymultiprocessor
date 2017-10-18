<?php
/**
 * Payment page class.
 *
 * This had to be done as a page to 'keep quickform from tampering'
 * (e.g with the submit).
 *
 * But this means we lose a lot of quickform's html generation.
 */
class CRM_Core_Page_PaymentPage extends CRM_Core_Page {


  /**
   * Page run function.
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  public function run() {
    $formData = $this->getTransparentRedirectFormData(CRM_Utils_Request::retrieve('key', 'String', CRM_Core_DAO::$_nullObject, TRUE));
    $paymentProcessorID = $formData['payment_processor_id'];
    $paymentProcessor = civicrm_api3('payment_processor', 'getsingle', array('id' => $paymentProcessorID));
    $contactID = $formData['contact_id'];

    $processor = Civi\Payment\System::singleton()->getByProcessor($paymentProcessor);

    $displayFields = $processor->getTransparentDirectDisplayFields();
    foreach ($displayFields as $fieldName => $displayField) {
      if ($displayField['htmlType'] == 'date') {
        $displayFields[$fieldName]['options']['year'] = $this->getDateFieldsYearOptions($displayField);
      }
      if (!empty($displayField['contact_api']) && !empty($contactID)) {
        $contact = civicrm_api3('Contact', 'get', array('id' => $contactID, 'sequential' => 1, 'options' => array('limit' => 1)));
        $displayFields[$fieldName]['options']['value'] = !empty($contact['values'][0]['display_name']) ? $contact['values'][0]['display_name'] : '';
      }
    }
    if (!empty($displayFields)) {
      CRM_Utils_System::setTitle(ts('Enter your payment details'));
    }
    $this->assign('hidden_fields', array_diff_key($formData, $displayFields));
    $this->assign('display_fields', $displayFields);
    $this->assign('post_url', $formData['post_submit_url']);
    return parent::run();
  }

  /**
   * Get the data required on the payment form.
   *
   * @param string $key
   *
   * @return array
   */
  protected function getTransparentRedirectFormData($key) {
    return json_decode(CRM_Core_Session::singleton()->get("transparent_redirect_data" . $key), TRUE);
  }

  /**
   * Get year options for date fields.
   *
   * Quickform would normally do this but we are operating as a page
   * to get around the off-site submit.
   *
   * @return mixed
   */
  protected function getDateFieldsYearOptions($field) {
    $options = array();
    if (isset($field['attributes']['minYear'])
    ) {
      $field['options']['year'] = array();
      $digits = CRM_Utils_Array::value('year_digits', $field, 4);
      $year = $field['attributes']['minYear'];
      while ($year <= $field['attributes']['maxYear']) {
        $options[substr($year, -$digits)] = $year;
        $year++;
      }
      return $options;
    }
    return $options;
  }

}
