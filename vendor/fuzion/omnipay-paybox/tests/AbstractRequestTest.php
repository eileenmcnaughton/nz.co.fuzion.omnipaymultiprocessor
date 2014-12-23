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

    /**
     * @var SystemPurchaseRequest
     */
    public $request;

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
            "PBX_IDENTIFIANT" => "107904482",
            "PBX_TOTAL" => "1000",
            "PBX_DEVISE" => "978",
            "PBX_CMD" => "TEST zyxwv",
            "PBX_PORTEUR" => "test@paybox.com",
            "PBX_RETOUR" => "Mt:M;Ref:R;Auto:A;Erreur:E",
            "PBX_TIME" => "2014-12-01T00:00:00+00:00",
        );
        $signature = $this->request->generateSignature($data);
        $expected = "7D15A9C93C4B1A3E11427C35A4CE539CD146182A8EE9E2964E82AA99385007D8B08823272399B384E037F53DEA5008F66F37011FEF44CD65A8B4D964FB55FD10";
        $this->assertEquals($expected, $signature);
    }
  public function testSignData2()
  {
      $data = array(
          "PBX_SITE" => "1999888",
          "PBX_RANG" => 32,
          "PBX_IDENTIFIANT" => "107904482",
          "PBX_TOTAL" => "100",
          "PBX_DEVISE" => "840",
          "PBX_CMD" => "3",
          "PBX_PORTEUR" => "admin@example.com",
          "PBX_RETOUR" => "Mt:M;Ref:R;Auto:A;Erreur:E",
          "PBX_TIME" => "2014-12-09T22:37:34+00:00",
      );
      $signature = $this->request->generateSignature($data);
      $expected = "3B1A2C5BCC1BCA095A5F129027613C86ADA1ED83A7D3C07A699A8664EA1B988C622E146FFBDE946BE7A26DBCA45FE840E6769DB6BC4D1DC79F8F186475AE143F";
      $this->assertEquals($expected, $signature);
  }
}

