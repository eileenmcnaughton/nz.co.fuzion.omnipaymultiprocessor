<?php

namespace tests;


use Omnipay\FirstAtlanticCommerce\Gateway;
use Omnipay\FirstAtlanticCommerce\Message\CreateCardResponse;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class CreateCardTest
 *
 * Test the creation of a stored card with FAC
 *
 * @package tests
 */
class CreateCardTest extends GatewayTestCase
{

    /** @var  Gateway */
    protected $gateway;
    /** @var  array */
    private $cardOptions;

    /**
     * Setup the gateway and card options for testing.
     */
    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setMerchantId('123');
        $this->gateway->setMerchantPassword('abc123');

        $this->cardOptions = [
            'customerReference'=>'John Doe',
            'card'=>$this->getValidCard()
        ];
    }

    /**
     * Test the successful creation of a credit card
     */
    public function testSuccessfulCardCreation()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');

        /** @var CreateCardResponse $response */
        $response = $this->gateway->createCard($this->cardOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getMessage());
        $this->assertEquals('411111_000011111', $response->getCardReference());
    }

    /**
     * Test the failed creation of a credit card
     */
    public function testFailedCardCreation()
    {
        $this->setMockHttpResponse('CreateCardFailure.txt');

        /** @var CreateCardResponse $response */
        $response = $this->gateway->createCard($this->cardOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Bad Card', $response->getMessage());
        $this->assertEmpty($response->getCardReference());
    }
}