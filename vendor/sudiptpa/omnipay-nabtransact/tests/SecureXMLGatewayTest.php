<?php

namespace Omnipay\NABTransact;

use Omnipay\Tests\GatewayTestCase;

class SecureXMLGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new SecureXMLGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('ABC0001');
    }

    public function testEcho()
    {
        $request = $this->gateway->echoTest(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);
        $this->assertInstanceOf('\Omnipay\NABTransact\Message\SecureXMLEchoTestRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\SecureXMLCaptureRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\SecureXMLPurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\SecureXMLRefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }
}
