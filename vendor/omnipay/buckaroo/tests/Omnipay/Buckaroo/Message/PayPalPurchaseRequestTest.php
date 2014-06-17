<?php

namespace Omnipay\Buckaroo\Message;

use Omnipay\Tests\TestCase;

class PayPalPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PayPalPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetEndpoint()
    {
        $this->assertSame('https://payment.buckaroo.nl/gateway/paypal_payment.asp', $this->request->getEndpoint());
    }
}
