<?php
/**
 * Created by PhpStorm.
 * User: eileen
 * Date: 17/06/2014
 * Time: 10:14 PM
 */
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC/Hook+Reference
return array(
  0 =>
    array(
      'name' => 'OmniPay - Stripe',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'OmniPay - Stripe',
          'name' => 'omnipay_Stripe',
          'description' => 'Omnipay Stripe Payment Processor',
          'user_name_label' => 'profile id',
          'password_label' => 'Secret Key',
          'signature_label' => 'Access Key',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://api.stripe.com/v1',
          'url_api_default' => 'https://api.stripe.com/v1',
          'billing_mode' => 1,
          'payment_type' => 1,
          'is_active' => 0,
        ),
    ),
  1 =>
    array(
      'name' => 'OmniPay - Cybersource',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'OmniPay - Cybersource',
          'name' => 'omnipay_Cybersource',
          'description' => 'Omnipay Cybersource Payment Processor',
          'user_name_label' => 'Profile ID',
          'password_label' => 'Secret Key',
          'signature_label' => 'Access Key',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
          'url_api_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
          'billing_mode' => 4,
          'payment_type' => 1,
        ),
    ),
  2 =>
    array(
      'name' => 'OmniPay - BitPay',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'OmniPay - BitPay',
          'name' => 'omnipay_BitPay',
          'description' => 'Omnipay BitPay Payment Processor',
          'user_name_label' => 'Api Key',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://bitpay.com/api',
          'url_api_default' => 'https://bitpay.com/api',
          'billing_mode' => 4,
          'payment_type' => 1,
        ),
    ),
  3 => array(
    'name' => 'OmniPay - Paypal',
    'entity' => 'payment_processor_type',
    'params' =>
      array(
        'version' => 3,
        'title' => 'OmniPay - Paypal',
        'name' => 'omnipay_PayPal',
        'description' => 'Omnipay PayPal Payment Processor',
        'user_name_label' => 'Username',
        'password_label' => 'Password',
        'signature_label' => 'Signature',
        'class_name' => 'Payment_OmnipayMultiProcessor',
        'url_site_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
        'url_api_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
        'billing_mode' => 4,
        'payment_type' => 1,
      ),
  ),
  4 => array(

  )
);
