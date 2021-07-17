<?php

namespace tests;


use Omnipay\FirstAtlanticCommerce\Gateway;
use Omnipay\FirstAtlanticCommerce\Message\AuthorizeResponse;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class AuthorizeTest
 *
 * Test the Authorize and Purchase requests and other supporting functions for those requests.
 *
 * @package tests
 */
class AuthorizeTest extends GatewayTestCase
{

    /** @var  Gateway */
    protected $gateway;

    private $purchaseOptions;

    /**
     * Setup the gateway and the purchase options to be used for testing.
     */
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('123');
        $this->gateway->setMerchantPassword('abc123');

        $this->purchaseOptions = [
            'amount'        => '10.00',
            'currency'      => 'USD',
            'transactionId' => '1234',
            'card'          => $this->getValidCard()
        ];
    }

    /**
     * Test the country formatting functionality
     */
    public function testFormatCountry()
    {
        //Alpha2
        $card = $this->getValidCard();
        $requestData = $this->getRequestData($card);
        $this->assertEquals(840, $requestData['BillingDetails']['BillToCountry']);

        //number
        $card['billingCountry'] = 840;
        $requestData = $this->getRequestData($card);
        $this->assertEquals(840, $requestData['BillingDetails']['BillToCountry']);

        //Alpha3
        $card['billingCountry'] = 'USA';
        $requestData = $this->getRequestData($card);
        $this->assertEquals(840, $requestData['BillingDetails']['BillToCountry']);
    }

    /**
     * Test the format state functionality with a good state
     */
    public function testFormatState()
    {
        $requestData = $this->getRequestData($this->getValidCard());
        $this->assertEquals('CA', $requestData['BillingDetails']['BillToState']);
    }

    /**
     * Test the format state functionality with a bad state
     *
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     */
    public function testBadState()
    {
        $card = $this->getValidCard();
        $card['billingState'] = 'California';
        $this->getRequestData($card);
    }

    /**
     * Test the format postal code functionality with a good code
     */
    public function testFormatPostCode()
    {
        $card = $this->getValidCard();
        $requestData = $this->getRequestData($card);
        $this->assertEquals('12345', $requestData['BillingDetails']['BillToZipPostCode']);

        $card['billingPostcode'] = '1 2-345';
        $requestData = $this->getRequestData($card);
        $this->assertEquals('12345', $requestData['BillingDetails']['BillToZipPostCode']);
    }


    /**
     * Test the format postal code functionality with a bad code
     *
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     */
    public function testBadPostCode()
    {
        $card = $this->getValidCard();
        $card['billingPostcode'] = '123#%';
        $this->getRequestData($card);
    }

    /**
     * @param $card
     *
     * @return array
     */
    private function getRequestData($card)
    {
        $purchaseOptions = $this->purchaseOptions;
        $purchaseOptions['card'] = $card;
        $request = $this->gateway->authorize($purchaseOptions);
        $requestData = $request->getData();
        return $requestData;
    }

    /**
     * Test a successful authorization
     */
    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        /** @var AuthorizeResponse $response */
        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1, $response->getCode());
        $this->assertEquals('Transaction is approved.', $response->getMessage());
        $this->assertEquals(307916543749, $response->getTransactionReference());
        $this->assertEquals(1234, $response->getTransactionId());
    }

    /**
     * Test a failed Authorization
     */
    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');

        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(2, $response->getCode());
        $this->assertEquals('Transaction is declined.', $response->getMessage());
        $this->assertEquals(307916543749, $response->getTransactionReference());
    }

    /**
     * Test a successful purchase. Purchases act the same as authorizations. They just have a different transaction code.
     * So the tests are basically the same.
     */
    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        /** @var AuthorizeResponse $response */
        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1, $response->getCode());
        $this->assertEquals('Transaction is approved.', $response->getMessage());
        $this->assertEquals(307916543749, $response->getTransactionReference());
        $this->assertEquals(1234, $response->getTransactionId());
    }

    /**
     * Test a failed purchase. Purchases act the same as authorizations. They just have a different transaction code.
     * So the tests are basically the same.
     */
    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');

        $response = $this->gateway->purchase($this->purchaseOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(2, $response->getCode());
        $this->assertEquals('Transaction is declined.', $response->getMessage());
        $this->assertEquals(307916543749, $response->getTransactionReference());
    }

    public function testAuthorizeWithTokenization()
    {
        $this->setMockHttpResponse('AuthorizeToken.txt');

        /** @var AuthorizeResponse $response */
        $response = $this->gateway->authorize($this->purchaseOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotEmpty($response->getCardReference());
    }
}