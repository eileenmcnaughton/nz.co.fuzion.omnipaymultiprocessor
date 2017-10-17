<?php

return array(
  'omnipay_test_mode' => array(
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
  ),
);
