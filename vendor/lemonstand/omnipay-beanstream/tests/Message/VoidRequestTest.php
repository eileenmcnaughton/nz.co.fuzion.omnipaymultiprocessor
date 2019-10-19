<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

/**
 * Class VoidRequestTest
 *
 * @package Omnipay\Beanstream\Message
 */
class VoidRequestTest extends TestCase
{

    /** @var RefundRequest */
    protected $request;

    /**
     * Setup the Void request object and initialize it with dummy data
     */
    public function setUp()
    {
        $this->request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount'=>10.00,
                'transactionReference'=>1000001
            )
        );
    }

    /**
     * Test that the endpoint is being generated correctly
     */
    public function testEndpoint()
    {
        $this->assertSame('https://www.beanstream.com/api/v1/payments/1000001/void', $this->request->getEndpoint());
    }

    /**
     * Test that the send function and response object are working correctly on success
     */
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('VoidSuccess.txt');
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('1000001', $response->getTransactionReference());
        $this->assertSame('1', $response->getOrderNumber());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('1', $response->getMessageId());
        $this->assertSame('TEST', $response->getAuthCode());
        $this->assertSame('VP', $response->getType());
        $this->assertNull($response->getCode());
        $responseCard = $response->getCard();
        $this->assertNotEmpty($responseCard);
        $this->assertSame('VI', $responseCard['card_type']);
        $this->assertSame('1234', $responseCard['last_four']);
        $this->assertSame(0, $responseCard['cvd_match']);
        $this->assertSame(0, $responseCard['address_match']);
        $this->assertSame(0, $responseCard['postal_result']);
    }

    /**
     * Test that the send function and response object are working correctly on failure
     */
    public function testSendError()
    {
        $this->setMockHttpResponse('VoidFailure.txt');
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