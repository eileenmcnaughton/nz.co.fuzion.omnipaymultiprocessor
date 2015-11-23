<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;
use Omnipay\NABTransact\Common\CreditCard;
use Omnipay\NABTransact\Tests\Lib;

class PeriodicDeleteCustomerRequestTest extends TestCase
{
    use Lib\fakerTrait;
	private static $request;
	
	public function setUp(){
		self::$request = new PeriodicDeleteCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
	}

    public function testDeleteGetData()
    {
		$expiry = explode('/', self::$faker->creditCardExpirationDateString);
		$details = [
            'merchantID' => 'XYZ0010',
            'password' => 'abcd1234',
			'testMode' => true,
			'apiVersion' => 'spxml-4.2',
			'customerReference' => '7e02b8b04a356b12f708',
		];

        self::$request->initialize($details);
		
        $data = self::$request->getData();

		$this->assertSame($details['customerReference'], $data['CustomerReferenceNumber']);

		return $details;
    }

	/**
	 * @depends testDeleteGetData
	 */
    public function testDeleteCustomerSendSuccess(array $details)
    {
		self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicDeleteCustomerRequestSuccess.txt');
        $response = self::$request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Successful', $response->getMessage());
		return $details;
    }
	/**
	 * @depends testDeleteGetData
	 */
    public function testDeleteCustomerSendFailure(array $details)
    {
		self::$request->initialize($details);

        $this->setMockHttpResponse('PeriodicDeleteCustomerRequestFailure.txt');
        $response = self::$request->send();
    
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('335', $response->getCode());
        $this->assertSame('CRN does not exist', $response->getMessage());
		return $details;
    }

}
