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
          'description' => 'Omnipay Strip Payment Processor',
          'user_name_label' => 'Secret Key',
          'password_label' => 'Publishable Key',
          'class_name' => 'Payment_OmnipayMultiProcessor',
          'url_site_default' => 'https://api.stripe.com/v1',
          'url_api_default' => 'https://api.stripe.com/v1',
          'billing_mode' => 1,
          'payment_type' => 1,
        ),
    ),

);
