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
class EwayTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use HttpClientTestTrait;
  use EwayRapidDirectTestTrait;
  use OmnipayTestTrait;

  /**
   * Parameters to use for submitting.
   *
   * @var array
   */
  protected $submitParams = [];

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * @throws \CiviCRM_API3_Exception
   */
  public function testTransparentDirectDisplayFields() {
    $processor = $this->createTestProcessor('Eway_Rapid');
    $processorObject = \Civi\Payment\System::singleton()->getById($processor['id']);
    /*var CRM_Core_Payment_OmnipayMultiProcessor $processorObject*/
    $fields = $processorObject->getTransparentDirectDisplayFields();
    $this->assertEquals('text', $fields['EWAY_CARDNAME']['htmlType']);
    $this->assertEquals('text', $fields['EWAY_CARDNUMBER']['htmlType']);
    $this->assertEquals('date', $fields['EWAY_CARDEXPIRY']['htmlType']);
    $this->assertEquals('text', $fields['EWAY_CARDCVN']['htmlType']);
  }

  /**
   * Test submitting a contribution page for a single payment.
   */
  public function testSubmitContributionPage() {
    $this->addMockTokenResponse();
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $processor = $this->createTestProcessor('Eway_RapidDirect');
    $this->createContributionPage($processor);

    $this->callAPISuccess('contribution_page', 'submit', $this->submitParams);
    $outbound = $this->getRequestBodies();

    $contribution = $this->callAPISuccessGetSingle('Contribution', ['payment_processor_id' => $processor['id'], 'contribution_status_id' => 'Completed']);

    $invoiceDescription = substr($contribution['contact_id'] . '-' . $contribution['id'] . '-Help Support CiviCRM!', 0, 24);

    $this->assertEquals('{"DeviceID":"https:\/\/github.com\/adrianmacneil\/omnipay","CustomerIP":"127.0.0.1","PartnerID":null,"ShippingMethod":null,"Customer":{"Title":null,"FirstName":"","LastName":"","CompanyName":"","Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"anthony_anderson@civicrm.org","Phone":"","CardDetails":{"Name":"","ExpiryMonth":"09","ExpiryYear":"30","CVN":234,"Number":"41111111111111111"}},"ShippingAddress":{"FirstName":"","LastName":"","Street1":null,"Street2":null,"City":null,"State":null,"Country":"","PostalCode":null,"Phone":null},"TransactionType":"Purchase","Payment":{"TotalAmount":10000,"InvoiceNumber":"' . $contribution['id'] . '","InvoiceDescription":"' . $invoiceDescription . '","CurrencyCode":"USD","InvoiceReference":null},"Method":"ProcessPayment"}',
      $outbound[0]);

  }

  /**
   * Test submitting a contribution page for a single payment.
   */
  public function testSubmitContributionPageEncrypted() {
    $this->addMockTokenResponse();
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $processor = $this->createTestProcessor('Eway_RapidDirect');
    $this->createContributionPage($processor, TRUE);

    $this->callAPISuccess('contribution_page', 'submit', $this->submitParams);
    $outbound = $this->getRequestBodies();

    $contribution = $this->callAPISuccessGetSingle('Contribution', ['payment_processor_id' => $processor['id'], 'contribution_status_id' => 'Completed']);

    $invoiceDescription = substr($contribution['contact_id'] . '-' . $contribution['id'] . '-Help Support CiviCRM!', 0, 24);

    $this->assertEquals($this->getRequest($contribution, $invoiceDescription),
      $outbound[0]);

  }

  /**
   * @param $processor
   * @param bool $isEncrypted
   *
   * @return array
   */
  protected function createContributionPage($processor, $isEncrypted = FALSE) {
    $processorID = $processor['id'];

    $contributionPageParams = [
      'title' => 'Help Support CiviCRM!',
      'financial_type_id' => 1,
      'is_monetary' => TRUE,
      'is_pay_later' => 1,
      'is_quick_config' => TRUE,
      'pay_later_text' => 'I will send payment by check',
      'pay_later_receipt' => 'This is a pay later reciept',
      'is_allow_other_amount' => 1,
      'min_amount' => 10.00,
      'max_amount' => 10000.00,
      'goal_amount' => 100000.00,
      'is_email_receipt' => 1,
      'is_active' => 1,
      'amount_block_is_active' => 1,
      'currency' => 'USD',
      'is_billing_required' => 0,
      'payment_processor' => $processorID,
    ];
    $contributionPageResult = $this->callAPISuccess('contribution_page', 'create', $contributionPageParams);

    // submit form values
    $priceSet = $this->callAPISuccess('price_set', 'getsingle', ['name' => 'default_contribution_amount']);

    $this->submitParams = [
      'id' => $contributionPageResult['id'],
      'email-5' => 'anthony_anderson@civicrm.org',
      'payment_processor_id' => $processorID,
      'amount' => 100.00,
      'tax_amount' => '',
      'currencyID' => 'AUD',
      'is_quick_config' => 1,
      'description' => 'Online Contribution: Help Support CiviCRM!',
      'price_set_id' => $priceSet['id'],
      'credit_card_number' => $this->getCardNumber($isEncrypted),
      'credit_card_exp_date' => ['M' => 9, 'Y' => 2030],
      'cvv2' =>$this->getCvv($isEncrypted),
    ];
    return [$contributionPageResult, $priceSet];
  }

  /**
   * @param $isEncrypted
   *
   * @return int
   */
  protected function getCardNumber($isEncrypted) {
    if (!$isEncrypted) {
      return '41111111111111111';
    }
    return 'eCrypted:ifA9U09ajEn++sMKdlWstGzIhA9Y7eqTFLzbNa5albBgL16iYQU7cKa0VHBrBUtBE1tmSFG9K7rlxhUFWIpO+OY36M1eI+cY2wSNbPBKUcdrkjFvdcLQTkSvqrfucKXXrM8T4Fcp5zpJoko8EICOcuWQIklT5Z0Ft2oUy6xYDvmSa2m+dywleZBSuLrxeYf+BKTVDCU+UMJsk70MqS3AgByOW5sFBLvhwbfDdxp8eWcAq4cPw7tTos3NobcndSSy2S1DhKrx6dlDVxPSLla0VGe2jDoopulDjTlub1E5dpvdCVFd2tv36A1YGvKI1To1RUyEk91+HzFoA==';
  }

  protected function getCvv($isEncrypted) {
    if (!$isEncrypted) {
      return 234;
    }
    return 'eCrypted:JqwlzOpX3mbaPNjoVWRtXmG4emlEPl8gossAHXBPgwcOBmqfJyWWYInaIATQYDG8dOSlReJNhA2h4lairQL1KIrJG1IaaTwbQVIx+P2PozfLAvmM1wmXkoHHpV8bSEJ4zqkNlX2bSkkh+V9iF6GKb4WEzCqFJbaJ5MXfp+mSRzvojfgvixV7rIUghRSCNe8+PYkvO4iJkhL5D3YFiEvJDvsRbHaD2NFJgK3be12Tp2hIyLAHKvN8hIiMnVqA+dBC03Y9ji20u9gU5KB2REWb6QsXoRuIESbqWESgxasJhbCfa4ov2F2RpLtb01dhAfipM2HZeGVHhmvhxTRpa0+w==';
  }

  /**
   * @param $contribution
   * @param $invoiceDescription
   * @return string
   */
  protected function getRequest($contribution, $invoiceDescription) {
    $cvvString = $this->getCvv(TRUE);
    if (!is_numeric($cvvString)) {
      $cvvString = '"' . addslashes($cvvString) . '"';
    }
    else {
      $cvvString = $cvvString;
    }
    $response = '{"DeviceID":"https:\/\/github.com\/adrianmacneil\/omnipay","CustomerIP":"127.0.0.1","PartnerID":null,"ShippingMethod":null,"Customer":{"Title":null,"FirstName":"","LastName":"","CompanyName":"","Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"anthony_anderson@civicrm.org","Phone":"","CardDetails":{"Name":"","ExpiryMonth":"09","ExpiryYear":"30","CVN":'
      . $cvvString . ',"Number":"'
      . $this->getCardNumber(TRUE)
      . '"}},"ShippingAddress":{"FirstName":"","LastName":"","Street1":null,"Street2":null,"City":null,"State":null,"Country":"","PostalCode":null,"Phone":null},"TransactionType":"Purchase","Payment":{"TotalAmount":10000,"InvoiceNumber":"' . $contribution['id'] . '","InvoiceDescription":"' . $invoiceDescription . '","CurrencyCode":"USD","InvoiceReference":null},"Method":"ProcessPayment"}';
    return $response;
  }
}
