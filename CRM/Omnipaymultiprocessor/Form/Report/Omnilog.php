<?php
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

class CRM_Omnipaymultiprocessor_Form_Report_Omnilog extends CRM_Extendedreport_Form_Report_ExtendedReport {

  public $_baseTable = 'civicrm_system_log';

  public function __construct() {
    if (!CRM_Core_Permission::check('administer payment processors')) {
      throw new CRM_Core_Exception(E::ts('Report only accessible with administer Payment Processors permission, may contain credentials'));
    }
    $this->_columns = $this->getColumns('SystemLog', [
      'fields' => TRUE,
      'fields_defaults' => ['message', 'context', 'timestamp'],
      'order_bys' => TRUE,
      'order_by_defaulys' => ['timestamp DESC'],
    ]);
    parent::__construct();
  }

  protected function getSystemLogColumns($options) {
    $specs = [
      'id' => [
        'name' => 'id',
        'title' => E::ts('System log ID'),
        'is_fields' => TRUE,
        'is_filters' => TRUE,
        'is_order_bys' => FALSE,
        'type' => CRM_Utils_Type::T_INT,
      ],
      'message' => [
        'name' => 'message',
        'title' => E::ts('Message'),
        'is_fields' => TRUE,
        'is_filters' => TRUE,
        'is_order_bys' => FALSE,
        'type' => CRM_Utils_Type::T_STRING,
      ],
      'context' => [
        'name' => 'context',
        'title' => E::ts('Detail'),
        'is_fields' => TRUE,
        'is_filters' => TRUE,
        'is_order_bys' => FALSE,
        'type' => CRM_Utils_Type::T_STRING,
        'alter_display' => 'alterDisplayDecodeJson',
      ],
      'level' => [
        'name' => 'level',
        'title' => E::ts('Level'),
        'is_fields' => TRUE,
        'is_filters' => TRUE,
        'is_order_bys' => TRUE,
        'type' => CRM_Utils_Type::T_STRING,
      ],
      'timestamp' => [
        'name' => 'timestamp',
        'title' => E::ts('Timestamp'),
        'is_fields' => TRUE,
        'is_filters' => TRUE,
        'is_order_bys' => TRUE,
        'type' => CRM_Utils_Type::T_TIMESTAMP,
      ],
    ];
    return $this->buildColumns($specs, 'civicrm_system_log', 'CRM_Core_DAO_SystemLog', NULL, $this->getDefaultsFromOptions($options), $options);
  }

  protected function alterDisplayDecodeJson($value) {
    $formatted = '<pre>' . str_replace(['\r\n', '\"', '{', '}', '\/', '},'], ['<br>', '&quot', '{<br>&nbsp', '<br>}', '/', '},<br>'], $value) . '</pre>';
    return str_replace('&quot,', '&quot,<br>', $formatted);
  }

}
