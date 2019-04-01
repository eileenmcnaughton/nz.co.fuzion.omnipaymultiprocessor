<?php
/**
 * Wrapper for Rest class permitting ajax api without the access AJAX API
 * for the specific action 'preapprove'.
 *
 * See https://github.com/eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor/issues/108
 */
class CRM_Utils_RestPreapprove extends CRM_Utils_REST {

  /**
   * Run ajax request.
   *
   * @return array
   */
  public static function ajax() {
    // We can't use a sanitised fn due to funky handling for word  'action' - fortunately
    // we are only using it for a php compare nothing risky.
    $action = strtolower(CRM_Utils_Array::value('action', $_REQUEST));
    $entity = str_replace('_', '', strtolower(CRM_Utils_Array::value('entity', $_REQUEST)));
    if (($action !== 'preapprove' || $entity !== 'paymentprocessor') && !CRM_Core_Permission::check(['access CiviCRM', 'access AJAX API'])) {
      CRM_Utils_System::permissionDenied();
      return NULL;
    }
    return CRM_Utils_REST::ajax();
  }

}
