<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class SecureXMLPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'testMode'            => true,
            'amount'              => '12.00',
            'transactionId'       => '1234',
            'card'                => [
                'number'         => '4444333322221111',
                'expiryMonth'    => '10',
                'expiryYear'     => '2030',
                'cvv'            => '123',
                'cardHolderName' => 'Sujip Thapa',
            ],
        ]);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('SecureXMLPurchaseRequestSendSuccess.txt');

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0', (string) $data->Payment->TxnList->Txn->txnType);
        $this->assertSame('1234', $response->getTransactionReference());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Approved', $response->getMessage());
    }
}
