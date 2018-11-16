<?php

namespace Omnipay\NABTransact;

use Omnipay\Tests\GatewayTestCase;

class DirectPostGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new DirectPostGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('XYZ0010');
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompleteAuthorize()
    {
        $request = $this->gateway->completeAuthorize(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\DirectPostPurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
}
