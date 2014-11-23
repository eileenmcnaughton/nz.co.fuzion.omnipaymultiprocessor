<?php

namespace Omnipay\Gopay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Gopay\Api\GopayHelper;
use Omnipay\Gopay\GatewayTest;
use Omnipay\Tests\TestCase;
use stdClass;

class PaymentStatusResponseTest extends TestCase {

    const SECURE_KEY = GatewayTest::SECURE_KEY;

    public static function paymentStatusWithState($sessionState)
    {
        $data = new stdClass();
        $data->targetGoId = 12345;
        $data->productName = 'Product Description';
        $data->totalPrice = 1000;
        $data->currency = 'CZK';
        $data->orderNumber = '1234';
        $data->recurrentPayment = '';
        $data->parentPaymentSessionId = '';
        $data->preAuthorization = '';
        $data->result = GopayHelper::CALL_COMPLETED;
        $data->sessionState = $sessionState;
        $data->sessionSubState = '';
        $data->paymentChannel = '';
        $data->paymentSessionId = 11112222;

        $data->encryptedSignature = GopayHelper::encrypt(
            GopayHelper::hash(GopayHelper::concatPaymentStatus($data, self::SECURE_KEY)),
            self::SECURE_KEY);
        return $data;
    }

    public function testCreatedState()
    {
        $data = self::paymentStatusWithState(GopayHelper::CREATED);

        $response = $this->createResponseFromData($data);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(11112222, $response->getTransactionReference());
    }

    public function testPaidState()
    {
        $data = self::paymentStatusWithState(GopayHelper::PAID);

        $response = $this->createResponseFromData($data);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals(11112222, $response->getTransactionReference());
    }

    public function testInvalidSignature()
    {
        $this->setExpectedException('Omnipay\Common\Exception\InvalidResponseException');

        $data = self::paymentStatusWithState(GopayHelper::CREATED);
        $data->encryptedSignature = '0123456789012345678901';

        $this->createResponseFromData($data);
    }

    /**
     * @param $data
     * @return PaymentStatusResponse
     */
    public function createResponseFromData($data)
    {
        $request = $this->getMockRequest();
        $request->shouldReceive('getParameters')->andReturn(array('secureKey' => self::SECURE_KEY));

        $response = new PaymentStatusResponse($request, $data);
        return $response;
    }
}
