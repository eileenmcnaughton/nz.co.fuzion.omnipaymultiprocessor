<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class UnionPayCompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new UnionPayCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testUnionPayCompletePurchaseSuccess()
    {
        $data = [];

        $data['restext'] = 'Approved';
        $data['rescode'] = '00';
        $data['summarycode'] = '1';
        $data['txnid'] = '12345';

        $response = new UnionPayCompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\UnionPayCompletePurchaseResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('00', $response->getCode());
        $this->assertTrue($response->summaryCode());
    }

    public function testUnionPayCompletePurchaseFailure()
    {
        $data = [];

        $data['restext'] = 'Error';
        $data['txnid'] = '12345';
        $data['summarycode'] = '3';
        $data['rescode'] = '06';

        $response = new UnionPayCompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\UnionPayCompletePurchaseResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNotSame('Approved', $response->getMessage());
        $this->assertSame('06', $response->getCode());
    }
}
