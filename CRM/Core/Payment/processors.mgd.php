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
  /*
   * Omnipay was enabled as a test- but it also requires a js script
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
  */
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
          'password_label' => 'Access Key',
          'signature_label' => 'Secret Key',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
          'url_api_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
          'billing_mode' => 4,
          'payment_type' => 3,
        ),
    ),
  2 =>
    array(
      'name' => 'OmniPay - Paybox System',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'OmniPay - PayboxSystem',
          'name' => 'omnipay_Paybox_System',
          'description' => 'Omnipay Paybox Payment Processor',
          'user_name_label' => 'Site',
          'password_label' => 'Identifiant',
          'signature_label' => 'Rang',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://dummyurl.com',
          'url_api_default' => 'https://dummyurl.com',
          'billing_mode' => 4,
          'payment_type' => 1,
        ),
    ),
  3 =>
    array(
      'name' => 'OmniPay - Paybox Direct',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'OmniPay - Paybox_Direct',
          'name' => 'omnipay_PayboxDirect',
          'description' => 'Omnipay Paybox Payment Processor',
          'user_name_label' => 'Site',
          'password_label' => 'Identifiant',
          'signature_label' => 'Rang',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://dummyurl.com',
          'url_api_default' => 'https://dummyurl.com',
          'billing_mode' => 1,
          'payment_type' => 1,
        ),
    ),
  4 =>
    array(
        'name' => 'OmniPay - GoPay',
        'entity' => 'payment_processor_type',
        'params' =>
            array(
                'version' => 3,
                'title' => 'OmniPay - GoPay',
                'name' => 'omnipay_Gopay',
                'description' => 'Omnipay GoPay Payment Processor',

                // DO NOT CHANGE: Labels are used as Omnipay gateway properties.
                'user_name_label' => 'Go Id',
                'password_label' => 'Secure Key',
                'signature_label' => '',

                'class_name' => 'Payment_OmnipayMultiProcessor',

                'url_site_default' => 'unused',
                'url_api_default' => 'unused',
                'url_site_test_default' => 'unused',
                'url_api_test_default' => 'unused',

                'billing_mode' => 4,
                'payment_type' => 1,
            ),
    ),
  /*
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
    'name' => 'OmniPay - Authorize AIM',
    'entity' => 'payment_processor_type',
    'params' =>
      array(
        'version' => 3,
        'title' => 'OmniPay - Authorize AIM',
        'name' => 'omnipay_AuthorizeNet_AIM',
        'description' => 'Omnipay OmniPay - Authorize AIM Payment Processor',
        'user_name_label' => 'Api Login ID',
        'password_label' => 'Transaction Key',
        'signature_label' => 'Hash Secret',
        'class_name' => 'Payment_OmnipayMultiProcessor',
        'url_site_default' => 'https://secure.authorize.net/gateway/transact.dll',
        'url_api_default' => 'https://secure.authorize.net/gateway/transact.dll',
        'billing_mode' => 1,
        'payment_type' => 1,
      ),
  ),
  5 => array(
    'name' => 'OmniPay - Authorize SIM',
    'entity' => 'payment_processor_type',
    'params' =>
      array(
        'version' => 3,
        'title' => 'OmniPay - Authorize SIM',
        'name' => 'omnipay_AuthorizeNet_SIM',
        'description' => 'Omnipay OmniPay - Authorize SIM Payment Processor',
        'user_name_label' => 'Api Login ID',
        'password_label' => 'Transaction Key',
        'signature_label' => 'Hash Secret',
        'class_name' => 'Payment_OmnipayMultiProcessor',
        'url_site_default' => 'https://secure.authorize.net/gateway/transact.dll',
        'url_api_default' => 'https://secure.authorize.net/gateway/transact.dll',
        'billing_mode' => 4,
        'payment_type' => 1,
      ),
  ),
  */
);
