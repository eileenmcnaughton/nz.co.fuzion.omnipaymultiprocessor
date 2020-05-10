<?php

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Symfony\Component\HttpFoundation\Request;

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
class IPNTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use OmnipayTestTrait;

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

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Example: Test that a version is returned.
   *
   * @throws \CiviCRM_API3_Exception
   * @throws \CRM_Core_Exception
   */
  public function testIPN() {
    $processor = $this->createTestProcessor('Mercanet_Offsite');
    Civi::$statics['Omnipay_Test_Config']['request'] = new Request();
    Civi::$statics['Omnipay_Test_Config']['request']->initialize(
      [],
      [
        'data' => 'captureDay=0|captureMode=AUTHOR_CAPTURE|currencyCode=978|merchantId=211000028030001|orderChannel=INTERNET|responseCode=00|transactionDateTime=2017-11-24T05:46:06+01:00|transactionReference=5071|keyVersion=2|acquirerResponseCode=00|amount=100|authorisationId=039830|panExpiryDate=201910|paymentMeanBrand=VISA|paymentMeanType=CARD|complementaryCode=00|complementaryInfo=<RULE_RESULT SC=0 \/>,<CARD_INFOS BDOM=XXX COUNTRY=NZL PRODUCTCODE=F NETWORK=VISA BANKCODE=00000 PRODUCTNAME=VISA CLASSIC PRODUCTPROFILE=C \/>|customerIpAddress=169.236.242.137|maskedPan=4988##########39|scoreProfile=Ass financementpartisenscommun|holderAuthentRelegation=N|holderAuthentStatus=ATTEMPT|transactionOrigin=INTERNET|paymentPattern=ONE_SHOT|customerMobilePhone=null|mandateAuthentMethod=null|mandateUsage=null|transactionActors=null|mandateId=null|captureLimitDate=20171124|dccStatus=null|dccResponseCode=null|dccAmount=null|dccCurrencyCode=null|dccExchangeRate=null|dccExchangeRateValidity=null|dccProvider=null|statementReference=null|panEntryMode=MANUAL|walletType=null|holderAuthentMethod=NOT_SPECIFIED|holderAuthentProgram=3DS|paymentMeanId=null|instalmentNumber=null|instalmentDatesList=null|instalmentTransactionReferencesList=null|instalmentAmountsList=null|settlementMode=null|mandateCertificationType=null|valueDate=null|creditorId=null|acquirerResponseIdentifier=null|acquirerResponseMessage=null|paymentMeanTradingName=null|additionalAuthorisationNumber=null|issuerWalletInformation=null|s10TransactionId=2|s10TransactionIdDate=20171124|preAuthenticationColor=null|preAuthenticationInfo=null|preAuthenticationProfile=null|preAuthenticationThreshold=null|preAuthenticationValue=null|invoiceReference=null|s10transactionIdsList=null|cardProductCode=F|cardProductName=VISA CLASSIC|cardProductProfile=C|issuerCode=00000|issuerCountryCode=NZL|acquirerNativeResponseCode=00|settlementModeComplement=null|preAuthorisationProfile=Ass financementpartisenscommun|preAuthorisationProfileValue=e917e2c0-701e-43a3-952b-89929fa7fbba|preAuthorisationRuleResultList=[{\"ruleCode\":\"SC\",\"ruleType\":\"NG\",\"ruleWeight\":\"D\",\"ruleSetting\":\"S\",\"ruleResultIndicator\":\"0\",\"ruleDetailedInfo\":\"TRANS=2:5;CUMUL=200:X\"}]|preAuthenticationProfileValue=null|preAuthenticationRuleResultList=null|paymentMeanBrandSelectionStatus=null|transactionPlatform=PROD',
      ]
    );
    try {
      CRM_Core_Payment_OmnipayMultiProcessor::processPaymentResponse(['processor_id' => $processor['id']]);
    }
    catch (Civi\Payment\Exception\PaymentProcessorException $e) {
      $this->assertEquals('An error may have occurred. Please check your receipt is correct5071Expected one Contribution but found 0', $e->getMessage());
      return;
    }
    $this->fail('Should not have reached this point');
  }

  /**
   * Example: Test that we're using a fake CMS.
   */
  public function testWellFormedUF() {
    $this->assertEquals('UnitTests', CIVICRM_UF);
  }

}
