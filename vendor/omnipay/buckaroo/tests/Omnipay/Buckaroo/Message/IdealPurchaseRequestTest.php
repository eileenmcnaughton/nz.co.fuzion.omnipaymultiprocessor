<?php

namespace Omnipay\Buckaroo\Message;

use Omnipay\Tests\TestCase;

class IdealPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new IdealPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetEndpoint()
    {
        $this->assertSame('https://payment.buckaroo.nl/gateway/ideal_payment.asp', $this->request->getEndpoint());
    }
}
