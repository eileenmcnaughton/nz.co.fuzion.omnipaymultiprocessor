<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DirectPostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount'              => '12.00',
            'returnUrl'           => 'https://www.example.com/return',
            'card'                => [
                'number'      => '4444333322221111',
                'expiryMonth' => '06',
                'expiryYear'  => '2020',
                'cvv'         => '123',
            ],
        ]);
    }

    public function testFingerprint()
    {
        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20161125123332';

        $this->assertSame('cbb64e71e360dba6337f22a00bdf0d0b89a7a568',
            $this->request->generateFingerprint($data));
    }

    public function testSend()
    {
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\DirectPostAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());

        $this->assertSame('https://transact.nab.com.au/live/directpostv2/authorise', $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());

        $data = $response->getData();
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
        $this->assertSame('0', $data['EPS_TXNTYPE']);
    }
}
