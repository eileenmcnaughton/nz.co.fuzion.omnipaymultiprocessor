<?php

namespace Omnipay\NABTransact;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\NABTransact\Tests\Lib;

class PeriodicGatewayTest extends GatewayTestCase
{
    use Lib\fakerTrait;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new PeriodicGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testCreateCard()
    {
        $expiry = explode('/', self::$faker->creditCardExpirationDateString);
        $card = [
            'number' => self::$faker->creditCardNumber,
            'expiryMonth' => $expiry[0],
            'expiryYear' => $expiry[1],
            'cvv' => self::$faker->randomNumber(3),
        ];

        $request = $this->gateway->createCard(['card' => $card]);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\PeriodicCreateCustomerRequest', $request);
        return ['customerReference' => '1234abcd', 'card' => $card];
    }

    /**
     * @depends testCreateCard
     */
    public function testUpdateCard(array $details)
    {
        $expiry = explode('/', self::$faker->creditCardExpirationDateString);
        $details['card'] = [
            'number' => self::$faker->creditCardNumber,
            'expiryMonth' => $expiry[0],
            'expiryYear' => $expiry[1],
            'cvv' => self::$faker->randomNumber(3),
        ];

        $request = $this->gateway->updateCard($details);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\PeriodicUpdateCustomerRequest', $request);
        $this->assertEquals($request->getCustomerReference(), $details['customerReference']);
        return $details;
    }

    /**
     * @depends testUpdateCard
     */
    public function testTriggerPayment(array $details)
    {
        unset($details['card']);
        $details['transactionReference'] = 'Testing Trigger Payment';
        $details['transactionAmount'] = 1234;
        $details['transactionCurrency'] = 'AUD';
        $request = $this->gateway->purchase($details);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\PeriodicTriggerPaymentRequest', $request);
        return $details;
    }

    /**
     * @depends testTriggerPayment
     */
    public function testDeleteCard(array $details)
    {
        $request = $this->gateway->deleteCard($details);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\PeriodicDeleteCustomerRequest', $request);
        return $details;
    }
}
