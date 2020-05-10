<?php
namespace Omnipay\Mercanet;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Mercanet\OffsiteGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\CreditCard;

class OffsiteGatewayTest extends GatewayTestCase
{
  /**
   * @var Omnipay/Mercanet/SystemGateway
   */
    public $gateway;

    /**
     * @var CreditCard
     */
    public $card;

    public function setUp()
    {
        parent::setUp();
        $this->setupGateway();;
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase(array('testMode' => TRUE, 'amount' => '10.00', 'currency' => 'EUR', 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit-mercanet.test.sips-atos.com/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    public function testAuthorize()
    {
        $response = $this->gateway->authorize(array('amount' => '10.00', 'currency' => 'EUR', 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit.mercanet.bnpparibas.net/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    public function testCapture()
    {
        $response = $this->gateway->capture(array('amount' => '10.00', 'currency' => 'EUR', 'card' => $this->card, 'testMode' => true))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit-mercanet.test.sips-atos.com/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    /**
     * Test offsite notify correctly loads.
     */
    public function testOffsiteNotify()
    {
        $this->getHttpRequest()->initialize(
            [],
            [
              'data' => 'captureDay=0|captureMode=AUTHOR_CAPTURE|currencyCode=978|merchantId=211000028030001|orderChannel=INTERNET|responseCode=00|transactionDateTime=2017-11-24T05:46:06+01:00|transactionReference=5071|keyVersion=2|acquirerResponseCode=00|amount=100|authorisationId=039830|panExpiryDate=201910|paymentMeanBrand=VISA|paymentMeanType=CARD|complementaryCode=00|complementaryInfo=<RULE_RESULT SC=0 \/>,<CARD_INFOS BDOM=XXX COUNTRY=NZL PRODUCTCODE=F NETWORK=VISA BANKCODE=00000 PRODUCTNAME=VISA CLASSIC PRODUCTPROFILE=C \/>|customerIpAddress=169.236.242.137|maskedPan=4988##########39|scoreProfile=Ass financementpartisenscommun|holderAuthentRelegation=N|holderAuthentStatus=ATTEMPT|transactionOrigin=INTERNET|paymentPattern=ONE_SHOT|customerMobilePhone=null|mandateAuthentMethod=null|mandateUsage=null|transactionActors=null|mandateId=null|captureLimitDate=20171124|dccStatus=null|dccResponseCode=null|dccAmount=null|dccCurrencyCode=null|dccExchangeRate=null|dccExchangeRateValidity=null|dccProvider=null|statementReference=null|panEntryMode=MANUAL|walletType=null|holderAuthentMethod=NOT_SPECIFIED|holderAuthentProgram=3DS|paymentMeanId=null|instalmentNumber=null|instalmentDatesList=null|instalmentTransactionReferencesList=null|instalmentAmountsList=null|settlementMode=null|mandateCertificationType=null|valueDate=null|creditorId=null|acquirerResponseIdentifier=null|acquirerResponseMessage=null|paymentMeanTradingName=null|additionalAuthorisationNumber=null|issuerWalletInformation=null|s10TransactionId=2|s10TransactionIdDate=20171124|preAuthenticationColor=null|preAuthenticationInfo=null|preAuthenticationProfile=null|preAuthenticationThreshold=null|preAuthenticationValue=null|invoiceReference=null|s10transactionIdsList=null|cardProductCode=F|cardProductName=VISA CLASSIC|cardProductProfile=C|issuerCode=00000|issuerCountryCode=NZL|acquirerNativeResponseCode=00|settlementModeComplement=null|preAuthorisationProfile=Ass financementpartisenscommun|preAuthorisationProfileValue=e917e2c0-701e-43a3-952b-89929fa7fbba|preAuthorisationRuleResultList=[{\"ruleCode\":\"SC\",\"ruleType\":\"NG\",\"ruleWeight\":\"D\",\"ruleSetting\":\"S\",\"ruleResultIndicator\":\"0\",\"ruleDetailedInfo\":\"TRANS=2:5;CUMUL=200:X\"}]|preAuthenticationProfileValue=null|preAuthenticationRuleResultList=null|paymentMeanBrandSelectionStatus=null|transactionPlatform=PROD',
            ]
        );
        $this->setupGateway();
        $request = $this->gateway->acceptNotification([]);
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteNotifyRequest', $request);
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00',));

        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteCompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    /**
     * @expectedException Omnipay\Common\Exception\InvalidRequestException
     */
    public function testCompletePurchaseSendMissingEmail()
    {
        $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'USD', 'card' => array(
            'firstName' => 'Pokemon',
            'lastName' => 'The second',
        )))->send();
    }

    protected function setupGateway(): void {
        $this->gateway = new OffsiteGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantID('Billy');
        $this->gateway->setSecretKey('really_secure');
        $this->card = new CreditCard(['email' => 'mail@mail.com']);
    }
}
