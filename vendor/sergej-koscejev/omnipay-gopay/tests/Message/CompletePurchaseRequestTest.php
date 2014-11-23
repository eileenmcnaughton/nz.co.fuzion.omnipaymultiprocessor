<?php

namespace Omnipay\Gopay\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Gopay\Api\GopayHelper;
use Omnipay\Gopay\GatewayTest;
use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var CompletePurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->soapClient = $this->getMockFromWsdl(__DIR__ . '/../EPaymentServiceV2.wsdl');
    }

    public function testSendSuccess()
    {
        $notificationParams = array(
            'targetGoId' => 12345,
            'paymentSessionId' => 1111,
            'orderNumber' => 2222,
            'encryptedSignature' => GopayHelper::getPaymentIdentitySignature(
                12345, 1111, null, 2222, GatewayTest::SECURE_KEY));

        $paymentStatusParams = array(
            'targetGoId' => 12345,
            'paymentSessionId' => 1111,
            'encryptedSignature' => GopayHelper::getPaymentSessionSignature(12345, 1111, GatewayTest::SECURE_KEY));

        $paymentStatusResponse = PaymentStatusResponseTest::paymentStatusWithState(GopayHelper::CREATED);

        $this->getHttpRequest()->query->replace($notificationParams);
        $this->soapClient->expects($this->once())->method('paymentStatus')
            ->with($paymentStatusParams)
            ->will($this->returnValue($paymentStatusResponse));

        $this->request = new CompletePurchaseRequest($this->soapClient, $this->getHttpClient(), $this->getHttpRequest());
        $this->request->setGoId('12345');
        $this->request->setSecureKey(GatewayTest::SECURE_KEY);

        $response = $this->request->send();

        $this->assertSame($paymentStatusResponse, $response->getData());
    }
}
