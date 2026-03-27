<?php

return [
  [
    'name'   => 'OmniPay - PayFast',
    'entity' => 'payment_processor_type',
    'metadata' => [
	    //'create_card_action' => 'purchase',
    ],
    'params' => [
      'version'         => 3,
      'title'           => 'OmniPay - PayFast',
      'name'            => 'omnipay_PayFast',
      'description'     => 'Omnipay PayFast Payment Processor',
      'class_name'      => 'Payment_OmnipayMultiProcessor',
      'user_name_label' => 'Merchant Id', 
      'password_label'  => 'Passphrase',
      'signature_label' => 'Merchant Key', 
      'url_site_default' => 'http://unused.com',
      'url_api_default' => 'http://unused.com',
      'url_recur_default' => 'http://unused.com',
      'url_site_test_default' => 'http://unused.com',
      'url_recur_test_default' => 'http://unused.com',
      'url_api_test_default' => 'http://unused.com',
      'billing_mode'    => 4,
      'payment_type'    => 1,
      'is_recur'        => true,
    ],
  ],
];