<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
$extensions = civicrm_api3('Extension', 'get', ['key' => 'nz.co.fuzion.extendedreport', 'status' => 'installed']);
if (!$extensions['count']) {
  return [];
}

return [
  [
    'name' => 'CRM_Omnipaymultiprocessor_Form_Report_Omnilog',
    'entity' => 'ReportTemplate',
    'params' => [
      'version' => 3,
      'label' => 'Omnilog',
      'description' => 'Debug log for omnipay - requires extended reports',
      'class_name' => 'CRM_Omnipaymultiprocessor_Form_Report_Omnilog',
      'report_url' => 'nz.co.fuzion.omnipaymultiprocessor/omnilog',
      'component' => 'CiviContribute',
    ],
  ],
];
