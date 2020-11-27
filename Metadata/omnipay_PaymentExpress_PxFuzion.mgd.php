<?php
/**
 * Created by PhpStorm.
 * User: eileen
 * Date: 17/06/2014
 * Time: 10:14 PM
 *
 * To add a new processor you need to add an item to this array. It is
 * sequentially numerically indexed and the important aspects are
 *
 * - name - omnipay_{Processor_Name}, Omnipay calls the gateway method create
 * with the processor name as the parameter. To get the processor name take a
 * look at the Readme for the gateway you are adding - you will generally see
 * The following gateways are provided by this package: Mollie so the name
 * should be ominpay_Mollie (note matching capitalisation)
 *
 * A more complex example is omnipay_SecurePay_DirectPayment.
 * This breaks down as
 *  - omnipay_ - our label within CiviCRM to denote Omnipay
 *  - SecurePay - the namespace as declared within the composer.json within the
 * securepay gateway
 *  - DirectPost - the prefix on the Gateway file. It is called
 * DirectPostGateway.php - this portion is excluded when the file is simply
 * named 'Gateway.php'
 *
 * - user_name_label, password_label, signature_label, subject_label - these
 * are generally about telling the plugin what to call these when they pass
 * them to Omnipay. They are also shown to users so some reformatting is done
 * to turn it into lower-first-letter camel case. Take a look at the gateway
 * file for your gateway. This is directly under src. Some provide more than
 * one and the 'getName' function distinguishes them. The getDefaultParameters
 * will tell you what to pass. eg if you see
 * 'apiKey' you should enter 'user_name' => 'Api Key' (you might ? be able to
 * get away with 'API Key' - need to check). You can provide as many or as few
 * as you want of these and it's irrelevant which field you put them in but
 * note that the signature field is the longest and that in future versions of
 * CiviCRM hashing may be done on password and signature on the screen.
 *
 * - 'class_name' => 'Payment_OmnipayMultiProcessor', (always)
 *
 * - 'url_site_default' - this is ignored. But, by giving one you make it
 * easier for people adding processors
 *
 * - 'billing_mode' - 1 = onsite, 4 = redirect offsite (including transparent
 * redirects).
 *
 * - payment_mode - 1 = credit card, 2 = debit card, 3 = transparent redirect.
 * In practice 3 means that billing details are gathered on-site so it may also
 * be used with automatic redirects where address fields need to be mandatory
 * for the signature.
 *
 * The record will be automatically inserted, updated, or deleted from the
 * database as appropriate. For more details, see "hook_civicrm_managed" at:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/Hook+Reference
 */
return [
  [
    'name' => 'OmniPay - Payment Express PxFusion',
    'entity' => 'payment_processor_type',
    'metadata' => [
      'is_transparent_redirect' => FALSE,
      'transparent_redirect' => [
        'fields' => [
          'CardHolderName' => ['core_field_name' => 'card_name'],
          'CardNumber' => ['core_field_name' => 'credit_card_number'],
          'Expiry' => [
            'core_field_name' => 'credit_card_exp_date',
            'month_field' => 'ExpiryMonth',
            'year_field' => 'ExpiryYear',
            'year_digits' => 2,
          ],
          'Cvc2' => ['core_field_name' => 'cvv2'],
        ],
        'billing_fields' => [],
      ],
    ],
    'params' => [
      'version' => 3,
      'title' => 'OmniPay - Payment Express PxFusion',
      'name' => 'omnipay_PaymentExpress_PxFusion',
      'description' => 'Omnipay Payment Express PxFusion Payment Processor',
      // DO NOT CHANGE: Labels are used as Omnipay gateway properties.
      'user_name_label' => 'Username',
      'password_label' => 'Password', // PxPay calls this "Key".
      'signature_label' => 'PxPostUsername',
      'subject_label' => 'PxPostPassword',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'url_site_default' => '',
      'url_api_default' => '',
      'url_recur_default' => '',
      'url_site_test_default' => '',
      'url_recur_test_default' => '',
      'url_api_test_default' => '',
      'billing_mode' => 1,
      'payment_type' => 3,
      'is_recur' => 1,
    ],
  ],
];
