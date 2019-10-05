<?php

namespace Omnipay\Beanstream\tests;

use Omnipay\Beanstream\Gateway;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class IntegrationTest
 *
 * This is an integration test class so it actually sends messages to Beanstream. This means you will need to setup a
 * test account with them and get your Merchant ID and API Passcode. Once you have those, you can create a new file in
 * the Mock folder called myCredentials.json and format it as below:
 *
 * {
 *      "merchantId":"<Your Merchant ID here>",
 *      "apiPasscode":"<Your Passcode here>"
 * }
 *
 * If that file does not exist or is not formatted in this way, all tests in this class will be skipped.
 *
 * @package Omnipay\Beanstream\tests
 */
class IntegrationTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    /**
     * Check for the credentials file. Skips the test if the credentials file is missing or not setup correctly. Otherwise,
     * instantiates the gateway and sets up the credentials.
     */
    public function setUp()
    {
        $merchantId = '';
        $apiPasscode = '';
        $credentialsFilePath = dirname(__FILE__) . '/Mock/myCredentials.json';

        if(file_exists($credentialsFilePath)) {
            $credentialsJson = file_get_contents($credentialsFilePath);
            if($credentialsJson) {
                $credentials = json_decode($credentialsJson);
                $merchantId = $credentials->merchantId;
                $apiPasscode = $credentials->apiPasscode;
            }
        }

        if(empty($merchantId) || empty($apiPasscode)) {
            $this->markTestSkipped();
        } else {
            $this->gateway = new Gateway();
            $this->gateway->setMerchantId($merchantId);
            $this->gateway->setApiPasscode($apiPasscode);
        }
    }

    /**
     * Test an Authorize call followed by a capture call for that transaction
     */
    public function testAuthCapture()
    {
        $card = $this->getValidCard();
        $card['number'] = '4030000010001234';
        $card['cvv'] = '123';
        $authResponse = $this->gateway->authorize(
            array(
                'amount'=>10.00,
                'card'=>$card,
                'payment_method'=>'card'
            )
        )->send();
        $this->assertTrue($authResponse->isSuccessful());
        $this->assertSame('Approved', $authResponse->getMessage());

        $captureResponse = $this->gateway->capture(
            array(
                'transactionReference'=>$authResponse->getTransactionReference(),
                'amount'=>10.00
            )
        )->send();

        $this->assertTrue($captureResponse->isSuccessful());
        $this->assertSame('Approved', $captureResponse->getMessage());
    }

    /**
     * Test a failed purchase transaction. The card number used below is a special one for Beanstream that always declines.
     */
    public function testFailedPurchase()
    {
        $card = $this->getValidCard();
        $card['number'] = '4003050500040005';
        $card['cvv'] = '123';
        $purchaseResponse = $this->gateway->purchase(
            array(
                'amount'=>10.00,
                'card'=>$card,
                'payment_method'=>'card'
            )
        )->send();

        $this->assertFalse($purchaseResponse->isSuccessful());
        $this->assertSame('DECLINE', $purchaseResponse->getMessage());
    }

    /**
     * Test a purchase call followed by a refund call for that purchase
     */
    public function testPurchaseRefund()
    {
        $card = $this->getValidCard();
        $card['number'] = '4030000010001234';
        $card['cvv'] = '123';
        $purchaseResponse = $this->gateway->purchase(
            array(
                'amount'=>20.00,
                'card'=>$card,
                'payment_method'=>'card'
            )
        )->send();

        $this->assertTrue($purchaseResponse->isSuccessful());
        $this->assertSame('Approved', $purchaseResponse->getMessage());

        $refundResponse = $this->gateway->refund(
            array(
                'amount'=>20.00,
                'transactionReference'=>$purchaseResponse->getTransactionReference()
            )
        )->send();

        $this->assertTrue($refundResponse->isSuccessful());
        $this->assertSame('Approved', $refundResponse->getMessage());
    }

    /**
     * Test a purchase call followed by a void call for that purchase
     */
    public function testPurchaseVoid()
    {
        $card = $this->getValidCard();
        $card['number'] = '4030000010001234';
        $card['cvv'] = '123';
        $purchaseResponse = $this->gateway->purchase(
            array(
                'amount'=>20.00,
                'card'=>$card,
                'payment_method'=>'card'
            )
        )->send();

        $this->assertTrue($purchaseResponse->isSuccessful());
        $this->assertSame('Approved', $purchaseResponse->getMessage());

        $voidResponse = $this->gateway->void(
            array(
                'amount'=>20.00,
                'transactionReference'=>$purchaseResponse->getTransactionReference()
            )
        )->send();

        $this->assertTrue($voidResponse->isSuccessful());
        $this->assertSame('Approved', $voidResponse->getMessage());
    }
}