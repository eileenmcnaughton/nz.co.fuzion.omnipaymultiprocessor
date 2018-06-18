<?php
/**
 * Created by PhpStorm.
 * User: eileen
 * Date: 17/06/2014
 * Time: 10:14 PM
 *
 * To add a new processor you need to add an item to this array. It is sequentially numerically indexed and the important aspects are
 *
 * - name - omnipay_{Processor_Name}, Omnipay calls the gateway method create with the processor name as the parameter.
 * To get the processor name take a look at the Readme for the gateway you are adding - you will generally see
 * The following gateways are provided by this package: Mollie so the name should be ominpay_Mollie (note matching capitalisation)
 *
 * A more complex example is omnipay_SecurePay_DirectPayment.
 * This breaks down as
 *  - omnipay_ - our label within CiviCRM to denote Omnipay
 *  - SecurePay - the namespace as declared within the composer.json within the securepay gateway
 *  - DirectPost - the prefix on the Gateway file. It is called DirectPostGateway.php - this portion is excluded when the file is simply
 *     named 'Gateway.php'
 *
 * - user_name_label, password_label, signature_label, subject_label - these are generally about telling the plugin what to call these when they pass them to
 * Omnipay. They are also shown to users so some reformatting is done to turn it into lower-first-letter camel case. Take a look at the gateway file for your gateway. This is directly under src.
 * Some provide more than one and the 'getName' function distinguishes them. The getDefaultParameters will tell you what to pass. eg if you see
 * 'apiKey' you should enter 'user_name' => 'Api Key' (you might ? be able to get away with 'API Key' - need to check). You can provide as many or as
 * few as you want of these and it's irrelevant which field you put them in but note that the signature field is the longest and that
 * in future versions of CiviCRM hashing may be done on password and signature on the screen.
 *
 * - 'class_name' => 'Payment_OmnipayMultiProcessor', (always)
 *
 * - 'url_site_default' - this is ignored. But, by giving one you make it easier for people adding processors
 *
 * - 'billing_mode' - 1 = onsite, 4 = redirect offsite (including transparent redirects).
 *
 * - payment_mode - 1 = credit card, 2 = debit card, 3 = transparent redirect. In practice 3 means that billing details are gathered on-site so
 * it may also be used with automatic redirects where address fields need to be mandatory for the signature.
 *
 * The record will be automatically inserted, updated, or deleted from the
 * database as appropriate. For more details, see "hook_civicrm_managed" at:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/Hook+Reference
 */
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
  2 => array(
    'name' => 'OmniPay - Paybox System',
    'entity' => 'payment_processor_type',
    'params' => array(
      'version' => 3,
      'title' => 'OmniPay - PayboxSystem',
      'name' => 'omnipay_Paybox_System',
      'description' => 'Omnipay Paybox Payment Processor',
      'user_name_label' => 'Site',
      'password_label' => 'Identifiant',
      'signature_label' => 'Key',
      'subject_label' => 'Rang',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'url_site_default' => 'https://dummyurl.com',
      'url_api_default' => 'https://dummyurl.com',
      'billing_mode' => 4,
      'payment_type' => 1,
    ),
  ),
  /*3 =>
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
  */
  4 => array(
    'name' => 'OmniPay - GoPay',
    'entity' => 'payment_processor_type',
    'params' => array(
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
  5 => array(
    'name' => 'OmniPay - Mollie',
    'entity' => 'payment_processor_type',
    'params' => array(
      'version' => 3,
      'title' => 'OmniPay - Mollie',
      'name' => 'omnipay_Mollie',
      'description' => 'Omnipay Mollie Payment Processor',
      'user_name_label' => 'apiKey',
      'password_label' => 'unused',
      'signature_label' => 'unused',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'url_site_default' => 'https://mollie.com',
      'url_api_default' => 'https://mollie.com',
      'billing_mode' => 4,
      'payment_type' => 1,
    ),
  ),
  /*

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

  7 => array(
    'name' => 'OmniPay - NABTransact_Transact',
    'entity' => 'payment_processor_type',
    'params' => array(
      'version' => 3,
      'title' => 'OmniPay - NAB Transact',
      'name' => 'omnipay_NABTransact_Transact',
      'description' => 'Omnipay NAB Transact',
      // DO NOT CHANGE: Labels are used as Omnipay gateway properties.
      'user_name_label' => 'Merchant Id',
      'password_label' => 'Password',
      'signature_label' => '',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'url_site_default' => 'http://unused.com',
      'url_api_default' => 'http://unused.com',
      'url_recur_default' => 'http://unused.com',
      'url_site_test_default' => 'http://unused.com',
      'url_recur_test_default' => 'http://unused.com',
      'url_api_test_default' => 'http://unused.com',
      'billing_mode' => 1,
      'payment_type' => 1,
      'is_recur' => 0,
    ),
  ),
  12 =>
    array(
      'name' => 'OmniPay - PayPal_Standard',
      'entity' => 'payment_processor_type',
      'params' =>
        array(
          'version' => 3,
          'title' => 'Omnipay - PayPal Standard',
          'name' => 'omnipay_Paypalstandard',
          'description' => 'Omnipay PayPal Standard',
          'user_name_label' => 'Merchant Account Email',
          'password_label' => 'unused',
          'signature_label' => 'unused',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://www.paypal.com/',
          'url_api_default' => '',
          'billing_mode' => 4,
          'payment_type' => 1,
        ),
    ),
  13 => array(
    'name' => 'OmniPay - PayPal_Pro',
    'entity' => 'payment_processor_type',
    'params' =>
      array(
        'version' => 3,
        'title' => 'OmniPay - PayPal Pro',
        'name' => 'omnipay_PayPal_Pro',
        'description' => 'PayPal_Pro Payment Processor',
        'user_name_label' => 'Username',
        'password_label' => 'Password',
        'signature_label' => 'Signature',
        'class_name' => 'Payment_OmnipayMultiProcessor',
        'url_site_default' => 'http://unused.com',
        'url_api_default' => 'http://unused.com',
        'url_recur_default' => 'http://unused.com',
        'url_site_test_default' => 'http://unused.com',
        'url_recur_test_default' => 'http://unused.com',
        'url_api_test_default' => 'http://unused.com',
        'billing_mode' => 4,
        'payment_type' => 1,
      ),
  ),
  14 => array(
    'name' => 'OmniPay - PayPal_Rest',
    'entity' => 'payment_processor_type',
    'params' =>
      array(
        'version' => 3,
        'title' => 'OmniPay - PayPal Rest',
        'name' => 'omnipay_PayPal_Rest',
        'description' => 'PayPal_Rest Payment Processor',
        'user_name_label' => 'clientId',
        'password_label' => 'secret',
        'class_name' => 'Payment_OmnipayMultiProcessor',
        'url_site_default' => 'http://unused.com',
        'url_api_default' => 'http://unused.com',
        'url_recur_default' => 'http://unused.com',
        'url_site_test_default' => 'http://unused.com',
        'url_recur_test_default' => 'http://unused.com',
        'url_api_test_default' => 'http://unused.com',
        'billing_mode' => 4,
        'payment_type' => 1,
      ),
  ),
);
