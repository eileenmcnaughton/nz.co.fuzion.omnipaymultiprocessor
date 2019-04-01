<?php

namespace Omnipay\NMI\Message;

use Omnipay\Tests\TestCase;

class DirectPostResponseTest extends TestCase
{
    public function testAuthorizeSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('DirectPostAuthSuccess.txt');
        $response = new DirectPostResponse($this->getMockRequest(), $httpResponse->getBody());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2577708057', $response->getTransactionReference());
        $this->assertSame('SUCCESS', $response->getMessage());
        $this->assertSame('1', $response->getCode());
        $this->assertSame('100', $response->getResponseCode());
        $this->assertSame('123456', $response->getAuthorizationCode());
        $this->assertSame('', $response->getAVSResponse());
        $this->assertSame('M', $response->getCVVResponse());
        $this->assertSame('', $response->getOrderId());
    }

    public function testAuthorizeFailure()
    {
        $httpResponse = $this->getMockHttpResponse('DirectPostAuthFailure.txt');
        $response = new DirectPostResponse($this->getMockRequest(), $httpResponse->getBody());
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('2577711599', $response->getTransactionReference());
        $this->assertSame('DECLINE', $response->getMessage());
        $this->assertSame('2', $response->getCode());
        $this->assertSame('200', $response->getResponseCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('', $response->getAVSResponse());
        $this->assertSame('M', $response->getCVVResponse());
        $this->assertSame('', $response->getOrderId());
    }
}
