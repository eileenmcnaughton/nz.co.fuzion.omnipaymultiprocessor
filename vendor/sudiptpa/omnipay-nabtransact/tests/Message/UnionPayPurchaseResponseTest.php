<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class UnionPayPurchaseResponseTest extends TestCase
{
    public function testConstruct()
    {
        $data = ['test' => '123'];

        $response = new UnionPayPurchaseResponse($this->getMockRequest(), $data, 'https://example.com/');

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertSame($data, $response->getData());

        $this->assertSame('https://example.com/', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame($data, $response->getRedirectData());
    }
}
