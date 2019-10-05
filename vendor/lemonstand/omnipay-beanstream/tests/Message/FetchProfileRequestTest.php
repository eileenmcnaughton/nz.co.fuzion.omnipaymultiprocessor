<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

class FetchProfileRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new FetchProfileRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize();
    }

    public function testSendSuccess()
    {
        $this->request->setProfileId('9ba60541d32648B1A3581670123dF2Ef');
        $this->setMockHttpResponse('FetchProfileSuccess.txt');
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame(1, $response->getCode());
        $this->assertSame('Operation Successful', $response->getMessage());
        $this->assertSame('9ba60541d32648B1A3581670123dF2Ef', $response->getCustomerCode());
    }

    public function testSendError()
    {
        $this->request->setProfileId('9ba60541d32648B1A3581670123dF2Ef');
        $this->setMockHttpResponse('FetchProfileFailure.txt');
        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertSame(15, $response->getCode());
        $this->assertSame(3, $response->getCategory());
        $this->assertSame('Customer code to modify does not exist', $response->getMessage());
    }

    public function testEndpoint()
    {
        $this->assertSame($this->request, $this->request->setProfileId('1'));
        $this->assertSame('1', $this->request->getProfileId());
        $this->assertSame('https://www.beanstream.com/api/v1/profiles/' . $this->request->getProfileId(), $this->request->getEndpoint());
    }

    public function testHttpMethod()
    {
        $this->assertSame('GET', $this->request->getHttpMethod());
    }

    public function testGetData()
    {
        $this->assertSame($this->request, $this->request->setProfileId('1'));
        $this->assertSame('1', $this->request->getProfileId());
        $this->assertNull($this->request->getData());
        $this->assertSame('GET', $this->request->getHttpMethod());
    }
}
