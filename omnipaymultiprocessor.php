<?php

require_once 'omnipaymultiprocessor.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @param $config
 */
function omnipaymultiprocessor_civicrm_config(&$config) {
  _omnipaymultiprocessor_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function omnipaymultiprocessor_civicrm_xmlMenu(&$files) {
  _omnipaymultiprocessor_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function omnipaymultiprocessor_civicrm_install() {
  CRM_Core_DAO::executeQuery("
    ALTER TABLE `civicrm_payment_processor`
    CHANGE COLUMN `signature` `signature` LONGTEXT NULL DEFAULT NULL;
  ");
  $logExists = CRM_Core_DAO::singleValueQuery("SHOW TABLES LIKE 'log_civicrm_payment_processor'");
  if ($logExists) {
    CRM_Core_DAO::executeQuery("
    ALTER TABLE `log_civicrm_payment_processor`
    CHANGE COLUMN `signature` `signature` LONGTEXT NULL DEFAULT NULL;
  ");
  }
  return _omnipaymultiprocessor_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function omnipaymultiprocessor_civicrm_uninstall() {
  return _omnipaymultiprocessor_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function omnipaymultiprocessor_civicrm_enable() {
  return _omnipaymultiprocessor_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function omnipaymultiprocessor_civicrm_disable() {
  return _omnipaymultiprocessor_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function omnipaymultiprocessor_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  //@todo fix me - when I grow up I want to be a proper upgrade hook (or possibly removed since there should be no new installs requiring this)
  CRM_Core_DAO::executeQuery("UPDATE civicrm_menu SET is_public = 1 WHERE path = 'civicrm/payment/details'");
  return _omnipaymultiprocessor_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @param array $entities
 */
function omnipaymultiprocessor_civicrm_managed(&$entities) {
  return _omnipaymultiprocessor_civix_civicrm_managed($entities);
}

/**
 * @param $version
 *
 * @return bool
 */
function omnipaymultiprocessor__versionAtLeast($version) {
  $codeVersion = explode('.', CRM_Utils_System::version());
  if (version_compare($codeVersion[0] . '.' . $codeVersion[1], $version) >= 0) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Implement buildForm hook to remove billing fields if elsewhere on the form.
 *
 * @param string $formName
 * @param CRM_Contribute_Form_Contribution_Main|CRM_Event_Form_Registration_Register $form
 */
function omnipaymultiprocessor_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_PaymentProcessor') {
    foreach (array('signature', 'test_signature') as $fieldName) {
      if ($form->elementExists($fieldName)) {
        $label = $form->_elements[$form->_elementIndex[$fieldName]]->_label;
        $form->removeElement($fieldName);
        $form->add('textarea', $fieldName,
          $label,
          array('rows' => 4, 'cols' => 40)
        );
      }
    }
    return;
  }

  if (!omnipaymultiprocessor_is_credit_card_form($formName)
    || $form->_paymentProcessor['class_name'] != 'Payment_OmnipayMultiProcessor'
    || omnipaymultiprocessor__versionAtLeast(4.7)) {
    return;
  }

  if (omnipaymultiprocessor__versionAtLeast(4.6)) {
    omnipaymultiprocessor_addBillingFieldsTo46Form($form);
    return;
  }

  $paymentType = civicrm_api3('option_value', 'getsingle', array('value' => $form->_paymentProcessor['payment_type'], 'option_group_id' => 'payment_type'));

  $form->assign('paymentTypeName', $paymentType['name']);

  $paymentFields = omnipaymultiprocessor_get_valid_form_payment_fields($formName == 'CRM_Contribute_Form_Contribution_Main' ? 'contribute' : 'event', $form->_paymentProcessor, (empty($form->_paymentFields) ? array() : $form->_paymentFields));
  if (!empty($paymentFields)) {
    $form->assign('paymentFields', $paymentFields);
    $form->assign('paymentTypeLabel', ts($paymentType['label'] . ' Information'));
  }
  else {
    $form->assign('paymentFields', NULL);
    $form->assign('paymentTypeLabel', NULL);
  }

  $billingDetailsFields = omnipaymultiprocessor_getBillingPersonalDetailsFields($form->_paymentProcessor);

  //we trick CiviCRM into adding the credit card form so we can remove the parts we don't want (the credit card fields)
  //for a transparent redirect like Cybersource
  $billingMode = $form->_paymentProcessor['billing_mode'];
  $form->_paymentProcessor['billing_mode'] = CRM_Core_Payment::BILLING_MODE_FORM;
  CRM_Core_Payment_Form::buildCreditCard($form);
  $form->_paymentProcessor['billing_mode'] = $billingMode;

  //CiviCRM assumes that if it is Not a credit card it MUST be a direct debit & makes those required
  $suppressedFields = omnipaymultiprocessor_get_suppressed_billing_fields((array) $billingDetailsFields, (array) $paymentFields, (array) $form->_paymentFields);

  foreach ($suppressedFields as $suppressedField) {
    $form->_paymentFields[$suppressedField]['is_required'] = FALSE;
  }
  $form->assign('suppressedFields', $suppressedFields);
  $form->assign('billingDetailsFields', $billingDetailsFields);

  CRM_Core_Region::instance('billing-block')->update('default', array(
    'disabled' => TRUE,
  ));
  CRM_Core_Region::instance('billing-block')->add(array(
    'template' => 'SubstituteBillingBlock.tpl',
  ));
}

/**
 * Add the billing field to the payment form if required.
 *
 * This requires 4.6.10 or greater due to an earlier bug. 4.7 should not require this.
 *
 * @param CRM_Core_Form $form
 */
function omnipaymultiprocessor_addBillingFieldsTo46Form(&$form) {
  $billingDetailsFields = omnipaymultiprocessor_getBillingPersonalDetailsFields($form->_paymentProcessor);
  if (!empty($billingDetailsFields)) {
    if (empty($form->_paymentFields)) {
      $form->_paymentFields = $billingDetailsFields;
    }
    $metadata = omnipaymultiprocessor_getBillingPersonalDetailsMetadata($form->_paymentProcessor);
    foreach ($metadata as $name => $field) {
      if (!empty($field['cc_field'])) {
        if (!empty($field['cc_field'])) {
          if ($field['htmlType'] == 'chainSelect') {
            $form->addChainSelect($field['name'], array('required' => FALSE));
          }
          else {
            $form->add($field['htmlType'],
              $field['name'],
              $field['title'],
              $field['attributes'],
              FALSE
            );
          }
        }
      }
      $requiredPaymentFields[$field['name']] = $field['is_required'];
    }
    $form->assign('requiredPaymentFields', $requiredPaymentFields);
    $form->assign('billingDetailsFields', $billingDetailsFields);
  }
  $form->billingFieldSets['billing_name_address-group']['fields'] = array();
}

/**
 * Get the billing fields we have suppressed
 * @param array $profileFields
 * @param integer $billingLocationID
 *
 * @return array
 */
function omnipaymultiprocessor_get_suppressed_billing_fields($billingDetailFields, $creditCardDetailsFields, $allFields) {
  return array_keys(array_diff_key($allFields, array_flip($billingDetailFields + $creditCardDetailsFields)));
}

/**
 * get billing fields
 * note we should consider calling the payment processor for this information like we do for payment fields
 * @param array $paymentProcessor
 *
 * @return array
 */
function omnipaymultiprocessor_getBillingPersonalDetailsFields($paymentProcessor) {
  $processor = omnipaymultiprocessor_get_payment_processor_object('contribute', $paymentProcessor);
  return $processor->getBillingBlockFields();
}

/**
 * get billing fields
 * note we should consider calling the payment processor for this information like we do for payment fields
 * @param array $paymentProcessor
 *
 * @return array
 */
function omnipaymultiprocessor_getBillingPersonalDetailsMetadata($paymentProcessor) {
  $processor = omnipaymultiprocessor_get_payment_processor_object('contribute', $paymentProcessor);
  return $processor->getBillingAddressFieldsMetadataPre47();
}

/**
 * Get a list of the payment fields to display on the form.
 *
 * @param $mode
 * @param $processor
 * @param $formPaymentFields
 *
 * @return array
 */
function omnipaymultiprocessor_get_valid_form_payment_fields($mode, $processor, $formPaymentFields) {
  $processor = omnipaymultiprocessor_get_payment_processor_object($mode, $processor);
  $paymentFields = $processor->getPaymentFormFields();
  return array_keys(array_intersect_key(array_fill_keys($paymentFields, 1), $formPaymentFields));
}

/**
 * get a list of civicrm's defined direct debit fields - because CiviCRM hard-codes the expectation that if it's not credit card it's debit card
 * we need to know these fields to hack around CiviCRM
 *
 * @return array
 */
function omnipaymultiprocessor_get_direct_debit_fields() {
  return array(
    'account_holder',
    'bank_account_number',
    'bank_identification_number',
    'bank_name',
  );
}

/**
 * @param $mode
 * @param $paymentProcessor
 * @return CRM_Core_Payment_OmnipayMultiProcessor
 */
function omnipaymultiprocessor_get_payment_processor_object($mode, $paymentProcessor) {
  return new CRM_Core_Payment_OmnipayMultiProcessor($mode, $paymentProcessor);
}

/**
 * @param $formName
 *
 * @return bool
 */
function omnipaymultiprocessor_is_credit_card_form($formName) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main' || $formName == 'CRM_Event_Form_Registration_Register'
  )  {
    return TRUE;
  }
  return FALSE;
}
