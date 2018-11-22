<?php
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;


return [
  'omnipay_test_mode' => [
    'group_name' => 'omnipay',
    'group' => 'omnipay',
    'filter' => 'omnipay',
    'name' => 'omnipay_test_mode',
    'html_type' => 'checkbox',
    'type' => 'Boolean',
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('In test mode the transaction is sent to the test gateway regardless of mode.'),
    'title' => E::ts('Enable test mode for Omnipay'),
    'help_text' => E::ts('Success is assumed'),
    'default' => 0,
    'settings_pages' => ['omnipay-dev' => ['weight' => 10]],
  ],
  'omnipay_developer_mode' => [
    'group_name' => 'omnipay',
    'group' => 'omnipay',
    'filter' => 'omnipay',
    'name' => 'omnipay_developer_mode',
    'html_type' => 'checkbox',
    'type' => 'Boolean',
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('In omnipay developer mode guzzle tracks requests. In addition test numbers prefill'),
    'title' => E::ts('Enable developer mode for Omnipay'),
    'help_text' => '',
    'default' => 0,
    'settings_pages' => ['omnipay-dev' => ['weight' => 20]],
  ],
];
