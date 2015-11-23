<?php

namespace Omnipay\NABTransact;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\NABTransact\Tests\Lib;

class TransactGatewayTest extends GatewayTestCase
{
    use Lib\fakerTrait;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new TransactGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * Test purchase.
     */
    public function testPurchase()
    {
        $expiry = explode('/', self::$faker->creditCardExpirationDateString);
        $details['card'] = [
          'number' => self::$faker->creditCardNumber,
          'expiryMonth' => $expiry[0],
          'expiryYear' => $expiry[1],
          'cvv' => self::$faker->randomNumber(3),
        ];

        $details['transactionReference'] = 'Testing Trigger Payment';
        $details['transactionAmount'] = 1234;
        $details['transactionCurrency'] = 'AUD';
        $request = $this->gateway->purchase($details);

        $this->assertInstanceOf('Omnipay\NABTransact\Message\TransactPurchaseRequest', $request);

    }

}
