<?php

namespace Omnipay\NMI;


use Omnipay\Tests\GatewayTestCase;

/**
 * Class DirectPostGatewayIntegrationTest
 *
 * Tests the driver implementation by actually communicating with NMI using their demo account
 *
 * @package Omnipay\NMI
 */
class DirectPostGatewayIntegrationTest extends GatewayTestCase
{
    /** @var  DirectPostGateway */
    protected $gateway;
    /** @var  array */
    protected $purchaseOptions;

    /**
     * Instantiate the gateway and the populate the purchaseOptions array
     */
    public function setUp()
    {
        $this->gateway = new DirectPostGateway();
        $this->gateway->setUsername('demo');
        $this->gateway->setPassword('password');

        $this->purchaseOptions = array(
            'amount'=>'10.00',
            'card'=>$this->getValidCard()
        );
    }

    /**
     * Test an authorize transaction followed by a capture
     */
    public function testAuthorizeCapture()
    {
        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('SUCCESS', $response->getMessage());

        $captureResponse = $this->gateway->capture(array(
            'amount'=>'10.00',
            'transactionReference'=>$response->getTransactionReference()
        ))->send();

        $this->assertTrue($captureResponse->isSuccessful());
        $this->assertEquals('SUCCESS', $captureResponse->getMessage());
    }

    /**
     * Test a purchase transaction followed by a refund
     */
    public function testPurchaseRefund()
    {
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('SUCCESS', $response->getMessage());

        $refundResponse = $this->gateway->refund(array(
            'transactionReference'=>$response->getTransactionReference()
        ))->send();

        $this->assertTrue($refundResponse->isSuccessful());
        $this->assertEquals('SUCCESS', $refundResponse->getMessage());
    }

    /**
     * Test a purchase transaction followed by a void
     */
    public function testPurchaseVoid()
    {
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('SUCCESS', $response->getMessage());

        $voidResponse = $this->gateway->void(array(
            'transactionReference'=>$response->getTransactionReference()
        ))->send();

        $this->assertTrue($voidResponse->isSuccessful());
        $this->assertEquals('Transaction Void Successful', $voidResponse->getMessage());
    }
}