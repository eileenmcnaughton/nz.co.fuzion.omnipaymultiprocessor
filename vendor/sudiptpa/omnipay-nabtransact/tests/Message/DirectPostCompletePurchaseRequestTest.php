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
            '3cdf9934588f2fa4b00df5adb36aec486befdaecdbb3de4c2515d00e05391101',
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
            'fingerprint'          => 'd319e8852e94972f8ef3f330884a0f5db85c6341fd367d6823a99e9b93d917c0',
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
            'fingerprint'          => 'af490d2635a7ebe8e97313fbea61924a51f4b4d095e2cf7a2a81f8fc3f5ca919',
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
