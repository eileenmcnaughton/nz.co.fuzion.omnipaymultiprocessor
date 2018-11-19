<?php

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class EwayTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use HttpClientTestTrait;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testTransparentDirectDisplayFields() {
    /*var $processor*/
    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_Eway_Rapid',
      'user_name' => 'abd',
      'password' => 'def',
      'is_test' => 1,
    ]);
    $processorObject = \Civi\Payment\System::singleton()->getById($processor['id']);
    $fields = $processorObject->getTransparentDirectDisplayFields();
    $this->assertEquals('text', $fields['EWAY_CARDNAME']['htmlType']);
    $this->assertEquals('text', $fields['EWAY_CARDNUMBER']['htmlType']);
    $this->assertEquals('date', $fields['EWAY_CARDEXPIRY']['htmlType']);
    $this->assertEquals('text', $fields['EWAY_CARDCVN']['htmlType']);
  }


}
