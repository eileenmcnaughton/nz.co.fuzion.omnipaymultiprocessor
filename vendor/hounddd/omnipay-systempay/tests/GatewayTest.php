<?php

namespace Omnipay\SystemPay;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }



    /*
    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\SystemPay\Message\PurchaseRequest', $request);
        $this->assertSame('1000', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\SystemPay\Message\CompletePurchaseRequest', $request);
        $this->assertSame('1000', $request->getAmount());
    }
    */
}
