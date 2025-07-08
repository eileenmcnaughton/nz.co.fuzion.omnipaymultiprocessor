<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 * 
 * CiviCRM specific file to be used with 
 * eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor 
 */
return [
  [
    'name' => 'OmniPay - PowerTranz',
    'entity' => 'payment_processor_type',
    'params' => [
      'version' => 3,
      'title' => 'OmniPay - PowerTranz 3DS2',
      'name' => 'omnipay_PowerTranz',
      'description' => 'PowerTranz (formerly FAC) 3DS2 Payment Processor. This replaces the previous FAC processor.',
      'user_name_label' => 'PowerTranz ID',
      'password_label' => 'PowerTranz Password',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'billing_mode' => 1,
      'payment_type' => 1,
      'is_recur' => 0,
    ]
  ],
];
