<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class SecureXMLEchoTestRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLEchoTestRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'testMode'            => true,
        ]);
    }

    public function testSuccess()
    {
        $this->setMockHttpResponse('SecureXMLEchoTestRequestSuccess.txt');

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);
        $this->assertSame('Normal', $response->getMessage());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('000', $response->getStatusCode());
    }
}
