<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 *
 * CiviCRM specific file to be used with
 * eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor
 */
return [
    [
        'name' => 'OmniPay - FirstAtlanticCommerce FAC',
        'entity' => 'payment_processor_type',
        'params' => [
            'version' => 3,
            'title' => 'OmniPay - First Atlantic Commerce',
            'name' => 'omnipay_FirstAtlanticCommerce_FAC',
            'description' => 'OmniPay - First Atlantic Commerce Payment Processor',
            'user_name_label' => 'Fac Id',
            'password_label' => 'Fac Pwd',
            'class_name' => 'Payment_OmnipayMultiProcessor',
            'billing_mode' => 1,
            'payment_type' => 1,
            'is_recur' => 0,
        ]
    ],
];