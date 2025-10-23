<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use Civi\Test\Api3TestTrait;

/**
 * Sage pay tests for one-off payment.
 *
 * @group headless
 */
class BillingFieldsTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use Api3TestTrait;
  use HttpClientTestTrait;

  /**
   * ID of payment processor created for test.
   *
   * @var int
   */
  protected $paymentProcessorID;

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless(): \Civi\Test\CiviEnvBuilder {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * Setup for test.
   *
   * @throws \CRM_Core_Exception
   * @throws \CRM_Csore_Exception
   */
  public function setUp():void {
    parent::setUp();

    $paymentProcessorID = (int) $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_SagePay_Server',
      'user_name' => 'sagepay_user',
      'is_test' => 1,
      'name' => 'SagePay',
      'sequential' => 1,
    ])['values'][0]['id'];

    $this->processor = $this->callAPISuccess('PaymentProcessor', 'getsingle', [
      'id' => $paymentProcessorID,
    ]);
  }

  public function testStateProvinceNotMandatoryInSagePay(): void {
    $processor = new CRM_Core_Payment_OmnipayMultiProcessor('live', $this->processor);
    $fields = $processor->getBillingAddressFields(5);
    $this->assertArrayNotHasKey('state_province', $fields);
  }

}
