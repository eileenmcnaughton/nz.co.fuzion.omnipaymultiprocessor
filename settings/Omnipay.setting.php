<?php

return array(
  'omnipay_developer_mode' => array(
    'group_name' => 'omnipay',
    'group' => 'omnipay',
    'filter' => 'omnipay',
    'name' => 'omnipay_developer_mode',
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'In developer mode transactions are not passed to the payment processor (for Omnipay processors).',
    'title' => 'Enable developer mode for omnipay',
    'help_text' => 'Success is assumed',
    'default' => 0,
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
  ),
);
