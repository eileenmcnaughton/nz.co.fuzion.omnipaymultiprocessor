<?php

require_once 'omnipaymultiprocessor.civix.php';
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

/**
 * Implementation of hook_civicrm_config
 *
 * @param $config
 */
function omnipaymultiprocessor_civicrm_config(&$config) {
  _omnipaymultiprocessor_civix_civicrm_config($config);
  $extRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR;
  $include_path = $extRoot . DIRECTORY_SEPARATOR . 'vendor' . PATH_SEPARATOR . get_include_path( );
  set_include_path( $include_path );
  require_once 'vendor/autoload.php';
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
  _omnipaymultiprocessor_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function omnipaymultiprocessor_civicrm_uninstall() {
  _omnipaymultiprocessor_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function omnipaymultiprocessor_civicrm_enable() {
  _omnipaymultiprocessor_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function omnipaymultiprocessor_civicrm_disable() {
  return _omnipaymultiprocessor_civix_civicrm_disable();
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
  _omnipaymultiprocessor_civix_civicrm_managed($entities);
}

/**
 * @param string $entity
 * @param string $action
 * @param array $params
 * @param array $permissions
 */
function omnipaymultiprocessor_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['payment_processor']['preapprove'] = ['make online contributions'];
}

/**
 * Implements hook_alterMenu().
 *
 * @param array $items
 */
function omnipaymultiprocessor_civicrm_alterMenu(&$items) {
  $items['civicrm/ajax/rest']['page_callback'] = ['CRM_Utils_RestPreapprove', 'ajax'];
  $items['civicrm/ajax/rest']['access_arguments'][0][] = 'make online contributions';
}

function omnipaymultiprocessor_civicrm_navigationMenu(&$menu) {
  _omnipaymultiprocessor_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('Omnipay Developer Settings'),
    'name' => 'omnpay-dev',
    'url' => 'civicrm/settings/omnipay-dev',
    'permission' => 'administer payment processors',

  ]);
}

function omnipaymultiprocessor_civicrm_alterSettingsFolders(&$metaDataFolders) {
  _omnipaymultiprocessor_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function omnipaymultiprocessor_civicrm_preProcess($formName, &$form) {
  if ($formName === 'CRM_Contribute_Form_Contribution_Main') {
    $form->assign('isJsValidate', TRUE);
    CRM_Core_Resources::singleton()->addVars('form', ['suppressAlerts' => 1]);
    if (!empty($form->_values['is_recur'])) {
      $recurOptions = [
        'is_recur_interval' =>  $form->_values['is_recur_interval'],
        'frequency_unit' => $form->_values['recur_frequency_unit'],
        'is_recur_installments' => $form->_values['is_recur_installments'],
      ];

      if (!$recurOptions['is_recur_interval']) {
        $recurOptions['frequency_interval'] = 1;
      }
     CRM_Core_Resources::singleton()->addVars(
        'omnipay', $recurOptions
     );
    }
  }

}
