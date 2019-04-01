<?php
/**
 * Declaration for NMI processor.
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
return [
  [
    'name' => 'OmniPay - NMI_DirectPost"',
    'entity' => 'payment_processor_type',
    'params' => [
      'version' => 3,
      'title' => 'OmniPay - NMI',
      'name' => 'omnipay_NMI_DirectPost',
      'user_name_label' => 'username',
      'password_label' => 'password',
      'description' => 'NMI (Network Merchants Inc.) driver for the Omnipay PHP payment processing library',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'billing_mode' => 1,
    ],
  ],
];
