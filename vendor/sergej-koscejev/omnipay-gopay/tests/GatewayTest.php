<?php
namespace Omnipay\Gopay;

use Omnipay\Gopay\Api\GopayHelper;
use Omnipay\Gopay\Message\PaymentStatusResponseTest;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    const SECURE_KEY = '9876543210abcdef';
    const GO_ID = '12345';

    protected $gateway;
    protected $soapClient;
    protected $options;

    public function setUp()
    {
        parent::setUp();
        $this->soapClient = $this->getMockFromWsdl(__DIR__ . '/EPaymentServiceV2.wsdl');

        $this->gateway = new Gateway($this->soapClient, $this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setGoId(self::GO_ID);
        $this->gateway->setSecureKey(self::SECURE_KEY);

        $this->options = array(
            'card' => $this->getValidCard()
        );
    }

    public function testPurchase()
    {
        $soapResponse = PaymentStatusResponseTest::paymentStatusWithState(GopayHelper::CREATED);

        $this->soapClient->expects($this->once())->method('createPayment')
            ->with($this->anything())
            ->will($this->returnValue($soapResponse));

        $this->assertSame($soapResponse, $this->gateway->purchase($this->options)->send()->getData());
    }

    public function testCompletePurchase()
    {
        $soapResponse = PaymentStatusResponseTest::paymentStatusWithState(GopayHelper::CREATED);

        $this->soapClient->expects($this->once())->method('paymentStatus')
            ->with($this->anything())
            ->will($this->returnValue($soapResponse));

        $this->getHttpRequest()->query->replace(array(
            'targetGoId' => self::GO_ID,
            'paymentSessionId' => '98765',
            'orderNumber' => '111',
            'encryptedSignature' => GopayHelper::getPaymentIdentitySignature(self::GO_ID, '98765', null, '111',
                self::SECURE_KEY)));

        $this->assertSame($soapResponse, $this->gateway->completePurchase(array())->send()->getData());
    }
}
