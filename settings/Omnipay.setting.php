<?php

return [
  'omnipay_test_mode' => [
    'group_name' => 'omnipay',
    'group' => 'omnipay',
    'filter' => 'omnipay',
    'name' => 'omnipay_test_mode',
    'type' => 'Boolean',
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'In test mode the transaction is sent to the test gateway regardless of mode.',
    'title' => 'Enable test mode for Omnipay',
    'help_text' => 'Success is assumed',
    'default' => 0,
    'quick_form_type' => 'YesNo',
  ],
  'omnipay_developer_mode' => [
    'group_name' => 'omnipay',
    'group' => 'omnipay',
    'filter' => 'omnipay',
    'name' => 'omnipay_developer_mode',
    'type' => 'Boolean',
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'In omnipay_developer_mode guzzle tracks requests. In addition test numbers prefill',
    'title' => 'Enable developer mode for Omnipay',
    'help_text' => '',
    'default' => 0,
    'quick_form_type' => 'YesNo',
  ],
];
