<?php

namespace Omnipay\NABTransact;

use Omnipay\NABTransact\Tests\Lib;
use Omnipay\NABTransact\Common\CreditCard;
use PHPUnit_Framework_TestCase;

class CreditCardTest extends PHPUnit_Framework_TestCase
{
    use Lib\fakerTrait;

    public function testCreateCreditCard()
    {
		$expiry = explode('/', self::$faker->creditCardExpirationDateString);
		$details = [
            'number' => '4111222233334444',
            'expiryMonth' => $expiry[0],
            'expiryYear' => $expiry[1],
            'cvv' => self::$faker->randomNumber(3),
		];
		
		$cc = new CreditCard($details);
		
		$this->assertInstanceOf('Omnipay\NABTransact\Common\CreditCard', $cc);
		$this->assertEquals($cc->getNumberLastThree(), '444');
		$this->assertEquals($cc->getNumberFirstSix(), '411122');
		$this->assertEquals($cc->getNumberMasked(), '411122...444');
		$this->assertEquals($cc->getNumberMasked('x'), '411122xxx444');
    }
}
