<?php

namespace Omnipay\Mercanet\Message;

use Omnipay\Tests\TestCase;
use Mockery as m;

class CompleteAuthorizeResponseTest extends TestCase
{
    public function testCompleteAuthorizeResponseSuccess()
    {
        $response = new OffsiteCompleteAuthorizeResponse(
            $this->getMockRequest(),
            array(
                "captureDay" => "0",
                "captureMode" => "AUTHOR_CAPTURE",
                "currencyCode" => "978",
                "merchantId" => "211000000000000",
                "orderChannel" => "INTERNET",
                "responseCode" => "00",
                "transactionDateTime" => "2017-11-24T05:46:06+01:00",
                "transactionReference" => "5071",
                "keyVersion" => "2",
                "acquirerResponseCode" => "00",
                "amount" => "100",
                "authorisationId" => "039830",
                "panExpiryDate" => "201910",
                "paymentMeanBrand" => "VISA",
                "paymentMeanType" => "CARD",
                "complementaryCode" => "00",
                "complementaryInfo" => "<RULE_RESULT SC=0 \/>,<CARD_INFOS BDOM=XXX COUNTRY=NZL PRODUCTCODE=F NETWORK=VISA BANKCODE=00000 PRODUCTNAME=VISA CLASSIC PRODUCTPROFILE=C \/>",
                "customerIpAddress" => "169.0.0.1",
                "maskedPan" => "4977##########77",
                "scoreProfile" => "Ass financementpartisenscommun",
                "holderAuthentRelegation" => "N",
                "holderAuthentStatus" => "ATTEMPT",
                "transactionOrigin" => "INTERNET",
                "paymentPattern" => "ONE_SHOT",
                "customerMobilePhone" => "null",
                "mandateAuthentMethod" => "null",
                "mandateUsage" => "null",
                "transactionActors" => "null",
                "mandateId" => "null",
                "captureLimitDate" => "20171124",
                "dccStatus" => "null",
                "dccResponseCode" => "null",
                "dccAmount" => "null",
                "dccCurrencyCode" => "null",
                "dccExchangeRate" => "null",
                "dccExchangeRateValidity" => "null",
                "dccProvider" => "null",
                "statementReference" => "null",
                "panEntryMode" => "MANUAL",
                "walletType" => "null",
                "holderAuthentMethod" => "NOT_SPECIFIED",
                "holderAuthentProgram" => "3DS",
                "paymentMeanId" => "null",
                "instalmentNumber" => "null",
                "instalmentDatesList" => "null",
                "instalmentTransactionReferencesList" => "null",
                "instalmentAmountsList" => "null",
                "settlementMode" => "null",
                "mandateCertificationType" => "null",
                "valueDate" => "null",
                "creditorId" => "null",
                "acquirerResponseIdentifier" => "null",
                "acquirerResponseMessage" => "null",
                "paymentMeanTradingName" => "null",
                "additionalAuthorisationNumber" => "null",
                "issuerWalletInformation" => "null",
                "s10TransactionId" => "2",
                "s10TransactionIdDate" => "20171124",
                "preAuthenticationColor" => "null",
                "preAuthenticationInfo" => "null",
                "preAuthenticationProfile" => "null",
                "preAuthenticationThreshold" => "null",
                "preAuthenticationValue" => "null",
                "invoiceReference" => "null",
                "s10transactionIdsList" => "null",
                "cardProductCode" => "F",
                "cardProductName" => "VISA CLASSIC",
                "cardProductProfile" => "C",
                "issuerCode" => "00000",
                "issuerCountryCode" => "NZL",
                "acquirerNativeResponseCode" => "00",
                "settlementModeComplement" => "null",
                "preAuthorisationProfile" => "Ass financementpartisenscommun",
                "preAuthorisationProfileValue" => "e917e2c0-701e-43a3-952b-89929fa7fbba",
                "preAuthorisationRuleResultList" => "[{\"ruleCode\":\"SC\",\"ruleType\":\"NG\",\"ruleWeight\":\"D\",\"ruleSetting\":\"S\",\"ruleResultIndicator\":\"0\",\"ruleDetailedInfo\":\"TRANS=2:5;CUMUL=200:X\"}]",
                "preAuthenticationProfileValue" => "null",
                "preAuthenticationRuleResultList" => "null",
                "paymentMeanBrandSelectionStatus" => "null",
                "transactionPlatform" => "PROD"
            )
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('039830', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testCompleteAuthorizeResponseFailure()
    {
        $response = new OffsiteCompleteAuthorizeResponse($this->getMockRequest(), array('responseCode' => '88'));

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

}
