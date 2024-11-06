<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\TestCase;

class AIMResponseTest extends TestCase
{
    /**
     * @expectedException Omnipay\Common\Exception\InvalidResponseException
     */
    public function testConstructEmpty()
    {
        $response = new AIMResponse($this->getMockRequest(), '');
    }

    public function testAuthorizeSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184493132', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame('1', $response->getCode());
        $this->assertSame('1', $response->getReasonCode());
        $this->assertSame('GA4OQP', $response->getAuthorizationCode());
        $this->assertSame('Y', $response->getAVSCode());
    }

    public function testAuthorizeFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMAuthorizeFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
        $this->assertSame('3', $response->getCode());
        $this->assertSame('5', $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
    }

    public function testCaptureSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184494531', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame('1', $response->getCode());
        $this->assertSame('1', $response->getReasonCode());
        $this->assertSame('F51OYG', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
    }

    public function testCaptureFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMCaptureFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('The transaction cannot be found.', $response->getMessage());
        $this->assertSame('3', $response->getCode());
        $this->assertSame('16', $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
    }

    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AIMPurchaseSuccess.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('2184492509', $response->getTransactionReference());
        $this->assertSame('This transaction has been approved.', $response->getMessage());
        $this->assertSame('1', $response->getCode());
        $this->assertSame('1', $response->getReasonCode());
        $this->assertSame('JE6JM1', $response->getAuthorizationCode());
        $this->assertSame('Y', $response->getAVSCode());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AIMPurchaseFailure.txt');
        $response = new AIMResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('A valid amount is required.', $response->getMessage());
        $this->assertSame('3', $response->getCode());
        $this->assertSame('5', $response->getReasonCode());
        $this->assertSame('', $response->getAuthorizationCode());
        $this->assertSame('P', $response->getAVSCode());
    }
}
