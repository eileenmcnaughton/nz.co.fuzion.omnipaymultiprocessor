<?php

namespace tests;


use Omnipay\FirstAtlanticCommerce\Gateway;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class StatusTest
 *
 * Test the request to get the status of a transaction.
 *
 * @package tests
 */
class StatusTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;
    /** @var  array */
    private $statusOptions;

    /**
     * Setup the gateway and status options for testing.
     */
    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setMerchantId('123');
        $this->gateway->setMerchantPassword('abc123');

        $this->statusOptions = [
            'transactionId' => '1234'
        ];
    }

    /**
     * Test a successful status check.
     */
    public function testSuccessfulStatus()
    {
        $this->setMockHttpResponse('StatusSuccess.txt');

        $response = $this->gateway->status($this->statusOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('Transaction is approved.', $response->getMessage());
    }

    /**
     * Test a failed status check.
     */
    public function testFailedStatus()
    {
        $this->setMockHttpResponse('StatusFailure.txt');

        $response = $this->gateway->status($this->statusOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('No Response', $response->getMessage());
    }
}