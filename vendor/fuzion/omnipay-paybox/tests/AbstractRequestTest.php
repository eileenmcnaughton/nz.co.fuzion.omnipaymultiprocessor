<?php

namespace Omnipay\Paybox\Message;

use Mockery as m;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    /**
     * Key for test site - see http://www1.paybox.com/wp-content/uploads/2014/02/PayboxTestParameters_V6.2_EN.pdf
     * @var string
     */
    public $key = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';

    public function setUp()
    {
        $this->request = new SystemPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'currency' => 'AUD',
                'amount' => '12.00',
                'card' => $this->getValidCard(),
            )
        );
        $this->request->setKey($this->key);
    }

    public function testGetRequiredFields()
    {
        $fields = $this->request->getRequiredFields();
        $this->assertContains('email', $fields);
    }

    public function testSignData()
    {
        $data = array(
            "PBX_SITE" => "1999888",
            "PBX_RANG" => 32,
            "PBX_IDENTIFIANT" => "2",
            "PBX_TOTAL" => "1000",
            "PBX_DEVISE" => "978",
            "PBX_CMD" => "TESTPaybox",
            "PBX_PORTEUR" => "test@paybox.com",
            "PBX_RETOUR" => "Mt:M;Ref:R;Auto:A;Erreur:E",
            "PBX_HASH" => "SHA512",
            "PBX_TIME" => "2011-02-28T11:01:50+01:00",
        );
        $signature = $this->request->generateSignature($data);
        $expected = "F2A799494504F9E50E91E44C129A45BBA26D23F2760CDF92B93166652B9787463E12BAD4C660455FB0447F882B22256DE6E703AD6669B73C59B034AF0CFC7E";
        $this->assertEquals($expected, $signature);
    }
}

