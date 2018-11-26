<?php

namespace Omnipay\Razorpay\Tests;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Razorpay\CheckoutGateway;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new CheckoutGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setKeyID('random_key_id');
        $this->gateway->setKeySecret('random_key_secret');
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\Razorpay\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase();

        $this->assertInstanceOf('\Omnipay\Razorpay\Message\CompletePurchaseRequest', $request);
    }

    public function testGetDefaultParameters()
    {
        $parameters = $this->gateway->getDefaultParameters();

        $this->assertSame($parameters['key_id'], '');
        $this->assertSame($parameters['key_secret'], '');
    }

    public function testGetKeyId()
    {
        $keyId = $this->gateway->getKeyID();

        $this->assertSame($keyId, 'random_key_id');
    }

    public function testGetKeySecret()
    {
        $keySecret = $this->gateway->getKeySecret();

        $this->assertSame($keySecret, 'random_key_secret');
    }
}
