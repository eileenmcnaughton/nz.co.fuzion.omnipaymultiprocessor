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
      'name' => 'payment_type',
      'entity' => 'option_group',
      'params' =>
        array(
          'version' => 3,
          'title' => 'Payment Type',
          'name' => 'payment_type',
          'description' => 'Payment Processor Payment type (configured on processor)',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
        ),
    ),
  1 =>
    array(
      'name' => 'credit_card',
      'entity' => 'option_value',
      'params' =>
        array(
          'version' => 3,
          'option_group_id' => 'payment_type',
          'label' => 'Credit Card',
          'value' => 1,
          'name' => 'credit_card',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
        ),
    ),
  2 =>
    array(
      'name' => 'direct_debit',
      'entity' => 'option_value',
      'params' =>
        array(
          'version' => 3,
          'option_group_id' => 'payment_type',
          'label' => 'Direct Debit',
          'value' => 2,
          'name' => 'direct_debit',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
        ),
    ),
  3 =>
    array(
      'name' => 'credit_card_off_site_post',
      'entity' => 'option_value',
      'params' =>
        array(
          'version' => 3,
          'option_group_id' => 'payment_type',
          'label' => 'Credit Card',
          'value' => 3,
          'name' => 'credit_card_off_site_post',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
        ),
    ),
);
