<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 10/18/17
 * Time: 12:53 PM
 */
return array(
  array(
    'name' => 'OmniPay - Cybersource',
    'entity' => 'payment_processor_type',
    'metadata' => array(
      'billing_fields' => array(
        'first_name' => 'billing_first_name',
        'middle_name' => 'billing_middle_name',
        'last_name' => 'billing_last_name',
        'street_address' => "billing_street_address-{$billingLocationID}",
        'city' => "billing_city-{$billingLocationID}",
        'country' => "billing_country_id-{$billingLocationID}",
        'state_province' => "billing_state_province_id-{$billingLocationID}",
        'postal_code' => "billing_postal_code-{$billingLocationID}",
      ),
      'transparent_redirect' => array(
        'fields' => array(
          'card_type' => array(
            'core_field_name' => 'credit_card_type',
            'options' => array(
              '' => ts('- select -'),
              '001' => 'Visa',
              '002' => 'Mastercard',
              '003' => 'Amex',
              '004' => 'Discover',
            ),
          ),
          'card_number' => array('core_field_name' => 'credit_card_number'),
          'card_expiry_date' => array('core_field_name' => 'credit_card_exp_date'),
          'card_cvn' => array('core_field_name' => 'cvv2'),
        ),
      ),
    ),
    'params' => array(
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
);
