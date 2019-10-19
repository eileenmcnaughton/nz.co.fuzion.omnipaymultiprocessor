<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('1000001', $response->getTransactionReference());
        $this->assertSame('1', $response->getOrderNumber());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('1', $response->getMessageId());
        $this->assertSame('TEST', $response->getAuthCode());
        $this->assertSame('P', $response->getType());
        $this->assertNull($response->getCode());
        $responseCard = $response->getCard();
        $this->assertNotEmpty($responseCard);
        $this->assertSame('VI', $responseCard['card_type']);
        $this->assertSame('1234', $responseCard['last_four']);
        $this->assertSame(0, $responseCard['cvd_match']);
        $this->assertSame(0, $responseCard['address_match']);
        $this->assertSame(0, $responseCard['postal_result']);
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());
        $this->assertSame(49, $response->getCode());
        $this->assertSame(3, $response->getCategory());
        $this->assertSame('Invalid transaction request string', $response->getMessage());
        $this->assertSame('https://www.beanstream.com/docs/errors#49', $response->getReference());
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getOrderNumber());
        $this->assertNull($response->getType());
        $this->assertNull($response->getMessageId());
        $this->assertNull($response->getAuthCode());
        $responseCard = $response->getCard();
        $this->assertEmpty($responseCard);
    }

    public function testAuthorizeSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AuthorizeSuccess.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('1000001', $response->getTransactionReference());
        $this->assertSame('1', $response->getOrderNumber());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('1', $response->getMessageId());
        $this->assertSame('TEST', $response->getAuthCode());
        $this->assertSame('PA', $response->getType());
        $this->assertNull($response->getCode());
        $responseCard = $response->getCard();
        $this->assertNotEmpty($responseCard);
        $this->assertSame('VI', $responseCard['card_type']);
        $this->assertSame('1234', $responseCard['last_four']);
        $this->assertSame(0, $responseCard['cvd_match']);
        $this->assertSame(0, $responseCard['address_match']);
        $this->assertSame(0, $responseCard['postal_result']);
    }

    public function testAuthorizeFailure()
    {
        $httpResponse = $this->getMockHttpResponse('AuthorizeFailure.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());
        $this->assertSame(49, $response->getCode());
        $this->assertSame(3, $response->getCategory());
        $this->assertSame('Invalid transaction request string', $response->getMessage());
        $this->assertSame('https://www.beanstream.com/docs/errors#49', $response->getReference());
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getOrderNumber());
        $this->assertNull($response->getType());
        $this->assertNull($response->getMessageId());
        $this->assertNull($response->getAuthCode());
        $responseCard = $response->getCard();
        $this->assertEmpty($responseCard);
    }
}
