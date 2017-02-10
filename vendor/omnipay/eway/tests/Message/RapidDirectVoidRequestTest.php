<?php

namespace Omnipay\Eway\Message;

use Omnipay\Tests\TestCase;

class RapidDirectVoidRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RapidDirectVoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'apiKey' => 'my api key',
            'password' => 'secret',
            'transactionReference' => '4324324'
        ));
    }

    public function testGetData()
    {
        $this->request->initialize(array(
            'apiKey' => 'my api key',
            'password' => 'secret',
            'transactionReference' => '4324324'
        ));

        $data = $this->request->getData();

        $this->assertSame('4324324', $data['TransactionId']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RapidDirectVoidRequestSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('12921019', $response->getTransactionReference());
        $this->assertSame('Transaction Approved', $response->getMessage());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('RapidDirectVoidRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0', $response->getTransactionReference());
        $this->assertSame('Invalid Auth Transaction ID for Capture/Void', $response->getMessage());
        $this->assertSame('V6134', $response->getCode());
    }
}
