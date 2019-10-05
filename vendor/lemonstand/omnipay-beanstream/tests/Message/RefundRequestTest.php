<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

/**
 * Class RefundRequestTest
 *
 * @package Omnipay\Beanstream\Message
 */
class RefundRequestTest extends TestCase
{

    /** @var RefundRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount'=>10.00,
                'transactionReference'=>1000001
            )
        );
    }

    public function testEndpoint()
    {
        $this->assertSame('https://www.beanstream.com/api/v1/payments/1000001/returns', $this->request->getEndpoint());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('1000001', $response->getTransactionReference());
        $this->assertSame('1', $response->getOrderNumber());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('1', $response->getMessageId());
        $this->assertSame('TEST', $response->getAuthCode());
        $this->assertSame('R', $response->getType());
        $this->assertNull($response->getCode());
        $responseCard = $response->getCard();
        $this->assertNotEmpty($responseCard);
        $this->assertSame('VI', $responseCard['card_type']);
        $this->assertSame('1234', $responseCard['last_four']);
        $this->assertSame(0, $responseCard['cvd_match']);
        $this->assertSame(0, $responseCard['address_match']);
        $this->assertSame(0, $responseCard['postal_result']);
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->request->send();
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