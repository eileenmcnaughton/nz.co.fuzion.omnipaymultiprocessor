<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostCompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DirectPostCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGenerateResponseFingerprint()
    {
        $this->request->initialize([
            'amount'              => '465.18',
            'transactionPassword' => 'abcd1234',
        ]);

        $data = [
            'timestamp'   => '20190215173250',
            'merchant'    => 'XYZ0010',
            'refid'       => '222',
            'summarycode' => '2',
        ];

        $this->assertSame(
            'c424541f1cc72055386c81b5b6b021312424024cd6b7d0c4feb949126e642e87',
            $this->request->generateResponseFingerprint($data)
        );
    }

    public function testSuccess()
    {
        $this->request->initialize([
            'amount'              => '12.00',
            'transactionPassword' => 'abcd1234',
            'transactionId'       => 'ORDER-ZYX8',
        ]);

        $this->getHttpRequest()->query->replace([
            'timestamp'            => '20190215173250',
            'callback_status_code' => '-1',
            'fingerprint'          => 'f4c6c0a4a3dd3a3dc1202b12d20b471d9595f587474a84c51e8916f86a9fc7a5',
            'txnid'                => '271337',
            'merchant'             => 'XYZ0010',
            'restext'              => 'Approved',
            'rescode'              => '00',
            'expirydate'           => '20190215',
            'settdate'             => '20190215',
            'refid'                => 'ORDER-ZYX8',
            'pan'                  => '444433...111',
            'summarycode'          => '1',
        ]);

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\DirectPostCompletePurchaseResponse', $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('271337', $response->getTransactionReference());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('00', $response->getCode());
    }

    public function testFailure()
    {
        $this->request->initialize([
            'amount'              => '465.18',
            'transactionPassword' => 'abcd1234',
        ]);

        $this->getHttpRequest()->query->replace([
            'timestamp'            => '20190215173250',
            'callback_status_code' => '404',
            'fingerprint'          => 'f8a4ecc7a81a3f682b79a7995a2c15ee3bea10a55dedf7a1cf4b99298b0716e4',
            'txnid'                => '274279',
            'merchant'             => 'XYZ0010',
            'restext'              => 'Customer Dispute',
            'rescode'              => '18',
            'expirydate'           => '102030',
            'settdate'             => '20190215',
            'refid'                => 'ORDER-ZYX8',
            'pan'                  => '444433...111',
            'summarycode'          => '2',
        ]);

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\DirectPostCompletePurchaseResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('274279', $response->getTransactionReference());
        $this->assertSame('Customer Dispute', $response->getMessage());
        $this->assertSame('18', $response->getCode());
    }
}
