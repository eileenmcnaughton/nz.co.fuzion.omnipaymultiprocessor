<?php

use Civi\Test\Api3TestTrait;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use CiviOmniPay\GuzzleHttp\Psr7\Response;
use CiviOmniPay\Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

/**
 * What an IPN records when the contribution still has an outstanding balance.
 *
 * completetransaction() treats every notification as settling the whole
 * contribution, which overstates the ledger once part of the total has already
 * been paid. See dev/financial#174.
 *
 * @group headless
 */
class PartialPaymentIPNTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use Api3TestTrait;
  use HttpClientTestTrait;
  use OmnipayTestTrait;

  public function tearDown(): void {
    unset(Civi::$statics['Omnipay_Test_Config']);
    parent::tearDown();
  }

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless(): \Civi\Test\CiviEnvBuilder {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * @param float $total
   *
   * @return int
   */
  protected function createPendingContribution(float $total): int {
    $contactID = $this->callAPISuccess('Contact', 'create', [
      'contact_type' => 'Individual',
      'first_name' => 'Partial',
      'last_name' => 'Payment',
    ])['id'];
    return (int) $this->callAPISuccess('Contribution', 'create', [
      'contact_id' => $contactID,
      'financial_type_id' => 'Donation',
      'total_amount' => $total,
      'currency' => 'EUR',
      'contribution_status_id' => 'Pending',
    ])['id'];
  }

  /**
   * Hand the notification handler a paid Mollie payment for this contribution.
   *
   * @param int $processorID
   * @param int $contributionID
   * @param string $amount
   *   Amount as the gateway reports it.
   * @param string $currency
   *   Currency as the gateway reports it.
   */
  protected function sendMollieNotification(int $processorID, int $contributionID, string $amount, string $currency = 'EUR'): void {
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];
    $this->getMockClient()->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
      'resource' => 'payment',
      'id' => 'tr_test' . $contributionID,
      'status' => 'paid',
      'amount' => ['currency' => $currency, 'value' => $amount],
      'metadata' => ['transactionId' => (string) $contributionID],
    ])));
    $this->runNotification([
      'processor_id' => $processorID,
      'transactionReference' => 'tr_test' . $contributionID,
    ]);
  }

  /**
   * Run the notification handler, absorbing the redirect it ends with.
   *
   * @param array $params
   */
  protected function runNotification(array $params): void {
    try {
      CRM_Core_Payment_OmnipayMultiProcessor::processPaymentResponse($params);
    }
    catch (CRM_Core_Exception_PrematureExitException $e) {
      // The handler sends the browser on once it is done; in a test that is an exit.
    }
  }

  /**
   * @param int $contributionID
   *
   * @return array
   */
  protected function getPayments(int $contributionID): array {
    return (array) $this->callAPISuccess('Payment', 'get', [
      'contribution_id' => $contributionID,
      'sequential' => 1,
    ])['values'];
  }

  /**
   * @param int $contributionID
   *
   * @return string
   */
  protected function getContributionStatus(int $contributionID): string {
    $contribution = $this->callAPISuccess('Contribution', 'getsingle', ['id' => $contributionID]);
    return CRM_Core_PseudoConstant::getName('CRM_Contribute_BAO_Contribution', 'contribution_status_id', $contribution['contribution_status_id']);
  }

  /**
   * The scenario from dev/financial#174: 155 in total, 55 already paid, the
   * gateway settles the remaining 100.
   */
  public function testRemainingBalanceIsRecordedNotTheFullTotal(): void {
    $processor = $this->createTestProcessor('Mollie');
    $contributionID = $this->createPendingContribution(155);
    $this->callAPISuccess('Payment', 'create', [
      'contribution_id' => $contributionID,
      'total_amount' => 55,
    ]);

    $this->sendMollieNotification((int) $processor['id'], $contributionID, '100.00');

    $payments = $this->getPayments($contributionID);
    $this->assertCount(2, $payments, 'Expected the earlier payment plus this one.');
    $this->assertEquals(155, array_sum(array_column($payments, 'total_amount')), 'Payments should add up to the contribution total, not beyond it.');
    $this->assertEquals('Completed', $this->getContributionStatus($contributionID));
  }

  /**
   * A contribution paid in full on the first attempt: one payment for the whole
   * amount, and the contribution completes as it always did.
   */
  public function testSinglePaymentInFullStillCompletes(): void {
    $processor = $this->createTestProcessor('Mollie');
    $contributionID = $this->createPendingContribution(155);

    $this->sendMollieNotification((int) $processor['id'], $contributionID, '155.00');

    $payments = $this->getPayments($contributionID);
    $this->assertCount(1, $payments);
    $this->assertEquals(155, array_sum(array_column($payments, 'total_amount')));
    $this->assertEquals('Completed', $this->getContributionStatus($contributionID));
  }

  /**
   * A gateway that does not get its 200 sends the notification again. The second
   * one must not add a second payment for the same transaction.
   */
  public function testRepeatedNotificationIsRecordedOnce(): void {
    $processor = $this->createTestProcessor('Mollie');
    $contributionID = $this->createPendingContribution(155);
    $this->callAPISuccess('Payment', 'create', [
      'contribution_id' => $contributionID,
      'total_amount' => 55,
    ]);

    $this->sendMollieNotification((int) $processor['id'], $contributionID, '50.00');
    $this->sendMollieNotification((int) $processor['id'], $contributionID, '50.00');

    $payments = $this->getPayments($contributionID);
    $this->assertCount(2, $payments, 'The repeated notification should not add a payment.');
    $this->assertEquals(105, array_sum(array_column($payments, 'total_amount')));
    $this->assertEquals('Partially paid', $this->getContributionStatus($contributionID));
  }

  /**
   * Payment::create ignores a currency passed to it and books in the currency
   * of the contribution, so an amount the gateway reports in another currency
   * must not be recorded as if it were in the contribution's own.
   */
  public function testOtherCurrencyIsNotBookedAsIs(): void {
    $processor = $this->createTestProcessor('Mollie');
    $contributionID = $this->createPendingContribution(155);
    $this->callAPISuccess('Payment', 'create', [
      'contribution_id' => $contributionID,
      'total_amount' => 55,
    ]);

    $this->sendMollieNotification((int) $processor['id'], $contributionID, '100.00', 'USD');

    $amounts = array_map('floatval', array_column($this->getPayments($contributionID), 'total_amount'));
    $this->assertNotContains(100.0, $amounts, 'An amount reported in USD should not be booked as 100 EUR.');
    $this->assertLessThanOrEqual(155.0, array_sum($amounts), 'Nothing should be booked beyond the contribution total.');
  }

  /**
   * getAmount() is defined on the request in Omnipay; only some drivers add it
   * to their response. A gateway without it keeps the previous behaviour rather
   * than failing on an undefined method.
   */
  public function testGatewayWithoutAmountKeepsPreviousBehaviour(): void {
    $processor = $this->createTestProcessor('Mercanet_Offsite');
    $contributionID = $this->createPendingContribution(155);

    Civi::$statics['Omnipay_Test_Config']['request'] = new Request();
    Civi::$statics['Omnipay_Test_Config']['request']->initialize([], [
      'data' => 'captureDay=0|captureMode=AUTHOR_CAPTURE|currencyCode=978|merchantId=211000028030001'
      . '|orderChannel=INTERNET|responseCode=00|transactionDateTime=2026-09-20T05:46:06+01:00'
      . '|transactionReference=' . $contributionID . '|keyVersion=2|acquirerResponseCode=00'
      . '|amount=15500|authorisationId=039830|paymentMeanBrand=VISA|paymentMeanType=CARD'
      . '|transactionOrigin=INTERNET|paymentPattern=ONE_SHOT',
    ]);
    $this->runNotification(['processor_id' => $processor['id']]);

    $this->assertEquals('Completed', $this->getContributionStatus($contributionID));
  }

}
