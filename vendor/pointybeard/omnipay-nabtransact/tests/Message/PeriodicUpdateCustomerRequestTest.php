<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;
use Omnipay\NABTransact\Common\CreditCard;
use Omnipay\NABTransact\Tests\Lib;

class PeriodicUpdateCustomerRequestTest extends TestCase
{
    use Lib\fakerTrait;
	private static $request;
	
	public function setUp(){
		self::$request = new PeriodicUpdateCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
	}

    public function testUpdateGetData()
    {
		$expiry = explode('/', self::$faker->creditCardExpirationDateString);
		$details = [
            'merchantID' => 'XYZ0010',
            'password' => 'abcd1234',
			'testMode' => true,
			'apiVersion' => 'spxml-4.2',
			'customerReference' => '7e02b8b04a356b12f708',
			'card' => new CreditCard([
                'number' => self::$faker->creditCardNumber,
                'expiryMonth' => $expiry[0],
                'expiryYear' => $expiry[1],
                'cvv' => self::$faker->randomNumber(3),
			]),
		];

        self::$request->initialize($details);
		
        $data = self::$request->getData();

        $this->assertSame($details['card']->getNumber(), $data['Customer']['CardDetails']['Number']);
        $this->assertSame((int)$details['card']->getExpiryMonth(), (int)$data['Customer']['CardDetails']['ExpiryMonth']);
		$this->assertSame(
			(int) $details['card']->getExpiryYear(),
			(int) gmdate('Y', gmmktime(0, 0, 0, 1, 1, (int)$data['Customer']['CardDetails']['ExpiryYear']))
		);
		$this->assertSame($details['customerReference'], $data['CustomerReferenceNumber']);

		return $details;
    }

	/**
	 * @depends testUpdateGetData
	 */
    public function testUpdateCustomerSendSuccess(array $details)
    {
		self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicUpdateCustomerRequestSuccess.txt');
        $response = self::$request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Successful', $response->getMessage());
		return $details;
    }
	/**
	 * @depends testUpdateGetData
	 */
    public function testUpdateCustomerSendFailure(array $details)
    {
		self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicUpdateCustomerRequestFailure.txt');
        $response = self::$request->send();
    
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('303', $response->getCode());
        $this->assertSame('Invalid CRN', $response->getMessage());
		return $details;
    }

}
