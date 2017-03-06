<?php

require_once 'omnipaymultiprocessor.civix.php';

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
