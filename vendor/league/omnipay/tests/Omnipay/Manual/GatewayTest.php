<?php

namespace Omnipay\Manual;

use Omnipay\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => 1000
        );
    }

    public function testAuthorize()
    {
        $response = $this->gateway->authorize($this->options)->send();

        $this->assertInstanceOf('\Omnipay\Manual\Message\Response', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testCapture()
    {
        $response = $this->gateway->authorize($this->options)->send();

        $this->assertInstanceOf('\Omnipay\Manual\Message\Response', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchase()
    {
        $response = $this->gateway->authorize($this->options)->send();

        $this->assertInstanceOf('\Omnipay\Manual\Message\Response', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}
