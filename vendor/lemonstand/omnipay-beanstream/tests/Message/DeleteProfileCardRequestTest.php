<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

class DeleteProfileCardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DeleteProfileCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize();
    }

    public function testEndpoint()
    {
        $this->assertSame($this->request, $this->request->setProfileId('1'));
        $this->assertSame($this->request, $this->request->setCardId('2'));
        $this->assertSame('1', $this->request->getProfileId());
        $this->assertSame('2', $this->request->getCardId());
        $this->assertSame('https://www.beanstream.com/api/v1/profiles/' . $this->request->getProfileId() . '/cards/' . $this->request->getCardId(), $this->request->getEndpoint());
    }

    public function testHttpMethod()
    {
        $this->assertSame('DELETE', $this->request->getHttpMethod());
    }

    public function testGetData()
    {
        $this->assertSame($this->request, $this->request->setProfileId('1'));
        $this->assertSame($this->request, $this->request->setCardId('2'));
        $this->assertSame('1', $this->request->getProfileId());
        $this->assertSame('2', $this->request->getCardId());
        $this->assertNull($this->request->getData());
        $this->assertSame('DELETE', $this->request->getHttpMethod());
    }
}
