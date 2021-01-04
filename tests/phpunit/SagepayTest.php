<?php

use Civi\Api4\Contact;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Civi\Test\Api3TestTrait;

/**
 * Sage pay tests for one-off payment.
 *
 * @group headless
 */
class SagepayTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use Api3TestTrait;
  use HttpClientTestTrait;
  use SagepayTestTrait;

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
   * @throws \API_Exception
   * @throws \CRM_Core_Exception
   */
  public function setUp():void {
    parent::setUp();

    $this->_new = $this->getNewTransaction();
    $this->ids['Contact'] = (int) Contact::create(FALSE)->setValues([
      'first_name' => $this->getNewTransaction()['card']['firstName'],
      'last_name' => $this->getNewTransaction()['card']['lastName'],
      'contact_type' => 'Individual',
    ])->execute()->first()['id'];

    $this->paymentProcessorID = (int) $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_SagePay_Server',
      'user_name' => 'abc',
      'is_test' => 1,
      'sequential' => 1,
    ])['values'][0]['id'];

    $this->_contribution = $this->callAPISuccess('Contribution', 'create', [
      'contact_id' => $this->ids['Contact'],
      'contribution_status_id' => 'Pending',
      'financial_type_id' => 'Donation',
      'receive_date' => 'today',
      'total_amount' => $this->_new['amount'],
      'sequential' => 1,
    ])['values'][0];
  }

  /**
   * When a payment is made, the Sagepay transaction identifier `VPSTxId`,
   * a secret security key `SecurityKey` and the corresponding `qfKey`
   * must be saved as part of the `trxn_id` JSON.
   *
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function testDoSinglePayment(): void {
    Civi::$statics['Omnipay_Test_Config'] = [ 'client' => $this->getHttpClient() ];

    $this->setMockHttpResponse('SagepayOneOffPaymentSecret.txt');
    $transactionSecret = $this->getSagepayTransactionSecret();

    $payment = $this->callAPISuccess('PaymentProcessor', 'pay', [
      'payment_processor_id' => $this->paymentProcessorID,
      'amount' => $this->_new['amount'],
      'qfKey' => $this->getQfKey(),
      'currency' => $this->_new['currency'],
      'component' => 'contribute',
      'email' => $this->_new['card']['email'],
      'contactID' => $this->ids['Contact'],
      'contributionID' => $this->_contribution['id'],
      'contribution_id' => $this->_contribution['id'],
    ]);

    $contribution = $this->callAPISuccess('Contribution', 'get', [
      'return' => ['trxn_id'],
      'contact_id' => $this->ids['Contact'],
      'sequential' => 1,
    ]);

    // Reset session as this would come in from the sage server.
    CRM_Core_Session::singleton()->reset();
    $ipnParams = $this->getSagepayPaymentConfirmation($this->paymentProcessorID, $contribution['id']);
    $this->signRequest($ipnParams);
    try {
      CRM_Core_Payment_OmnipayMultiProcessor::processPaymentResponse(['processor_id' => $this->paymentProcessorID]);
    }
    catch (CRM_Core_Exception_PrematureExitException $e) {
      // Check we didn't try to redirect the server.
      $this->assertArrayNotHasKey('url', $e->errorData);
      $contribution = \Civi\Api4\Contribution::get(FALSE)
        ->addWhere('id', '=', $contribution['id'])
        ->addSelect('contribution_status_id:name', 'trxn_id')->execute()->first();
      $this->assertEquals('Completed', $contribution['contribution_status_id:name']);
    }
  }

}
