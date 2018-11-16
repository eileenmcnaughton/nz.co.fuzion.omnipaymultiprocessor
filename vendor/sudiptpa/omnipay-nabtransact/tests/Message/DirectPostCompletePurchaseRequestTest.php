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
            'timestamp'   => '20161125123332',
            'merchant'    => 'XYZ0010',
            'refid'       => '222',
            'summarycode' => '2',
        ];

        $this->assertSame(
            '79200d1df5dc3f914a90ce476fdef317d224629f',
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
            'timestamp'            => '20161125130241',
            'callback_status_code' => '-1',
            'fingerprint'          => 'e30eb8381bc41201fbdf54a021d8228a3fbb6a6f',
            'txnid'                => '271337',
            'merchant'             => 'XYZ0010',
            'restext'              => 'Approved',
            'rescode'              => '00',
            'expirydate'           => '20161126',
            'settdate'             => '20161126',
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
            'timestamp'            => '20161126051715',
            'callback_status_code' => '404',
            'fingerprint'          => 'cd75e2ef38cf63a2fa390024539acc7691eebc1d',
            'txnid'                => '274279',
            'merchant'             => 'XYZ0010',
            'restext'              => 'Customer Dispute',
            'rescode'              => '18',
            'expirydate'           => '102030',
            'settdate'             => '20161126',
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
