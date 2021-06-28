<?php
namespace tests;


use Omnipay\FirstAtlanticCommerce\Gateway;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class TransactionModificationTest
 *
 * Tests of the transaction modification procedures in FAC.
 *
 * @package tests
 */
class TransactionModificationTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;
    /** @var  array */
    private $options;

    /**
     * Setup the gateway and options for testing.
     */
    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('123');
        $this->gateway->setMerchantPassword('abc1233');

        $this->options = [
            'amount' => '10.00',
            'currency' => 'USD',
            'transactionId' => '1234'
        ];
    }

    /**
     * Test a successful capture.
     */
    public function testSuccessfulCapture()
    {
        $this->setMockHttpResponse('ModificationSuccess.txt');

        $response = $this->gateway->capture($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1101, $response->getCode());
        $this->assertEquals('Success', $response->getMessage());
    }

    /**
     * Test a failed capture.
     */
    public function testFailedCapture()
    {
        $this->setMockHttpResponse('ModificationFailed.txt');

        $response = $this->gateway->capture($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(1100, $response->getCode());
        $this->assertEquals('Failed', $response->getMessage());
    }

    /**
     * Test a successful refund.
     */
    public function testSuccessfulRefund()
    {
        $this->setMockHttpResponse('ModificationSuccess.txt');

        $response = $this->gateway->refund($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1101, $response->getCode());
        $this->assertEquals('Success', $response->getMessage());
    }

    /**
     * Test a failed refund.
     */
    public function testFailedRefund()
    {
        $this->setMockHttpResponse('ModificationFailed.txt');

        $response = $this->gateway->refund($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(1100, $response->getCode());
        $this->assertEquals('Failed', $response->getMessage());
    }

    /**
     * Test a successful Void.
     */
    public function testSuccessfulVoid()
    {
        $this->setMockHttpResponse('ModificationSuccess.txt');

        $response = $this->gateway->void($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1101, $response->getCode());
        $this->assertEquals('Success', $response->getMessage());
    }

    /**
     * Test a failed Void.
     */
    public function testFailedVoid()
    {
        $this->setMockHttpResponse('ModificationFailed.txt');

        $response = $this->gateway->void($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(1100, $response->getCode());
        $this->assertEquals('Failed', $response->getMessage());
    }
}