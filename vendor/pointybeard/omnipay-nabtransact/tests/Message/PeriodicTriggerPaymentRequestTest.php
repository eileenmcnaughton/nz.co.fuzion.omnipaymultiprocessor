<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;
use Omnipay\NABTransact\Common\CreditCard;
use Omnipay\NABTransact\Tests\Lib;

class PeriodicTriggerPaymentRequestTest extends TestCase
{
    use Lib\fakerTrait;
    private static $request;
    
    public function setUp(){
        self::$request = new PeriodicTriggerPaymentRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testTriggerGetData()
    {
        $expiry = explode('/', self::$faker->creditCardExpirationDateString);
        $details = [
            'merchantID' => 'XYZ0010',
            'password' => 'abcd1234',
            'testMode' => true,
            'apiVersion' => 'spxml-4.2',
            'customerReference' => '7e02b8b04a356b12f708',
            'transactionReference' => 'Test Trigger of CC Payment',
            'transactionAmount' => '1234',
            'transactionCurrency' => 'AUD',
        ];

        self::$request->initialize($details);
        
        $data = self::$request->getData();
        $this->assertSame($details['customerReference'], $data['CustomerReferenceNumber']);
        $this->assertSame($details['transactionReference'], $data['Transaction']['Reference']);
        $this->assertSame($details['transactionAmount'], $data['Transaction']['Amount']);
        $this->assertSame($details['transactionCurrency'], $data['Transaction']['Currency']);

        return $details;
    }

    /**
     * @depends testTriggerGetData
     */
    public function testTriggerPaymentSendSuccess(array $details)
    {
        self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicTriggerPaymentRequestSuccess.txt');
        $response = self::$request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('132887', $response->getTransactionId());
        return $details;
    }
    /**
     * @depends testTriggerGetData
     */
    public function testTriggerPaymentSendFailure(array $details)
    {
        self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicTriggerPaymentRequestFailure.txt');
        $response = self::$request->send();
    
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('95', $response->getCode());
        $this->assertSame('Reconcile Error', $response->getMessage());
        $this->assertSame('745425', $response->getTransactionId());
        return $details;
    }

}
