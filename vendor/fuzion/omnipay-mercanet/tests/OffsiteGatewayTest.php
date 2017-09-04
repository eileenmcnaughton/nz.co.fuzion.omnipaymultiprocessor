<?php
namespace Omnipay\Mercanet;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Mercanet\OffsiteGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\CreditCard;

class OffsiteGatewayTest extends GatewayTestCase
{
  /**
   * @var Omnipay/Mercanet/SystemGateway
   */
    public $gateway;

    /**
     * @var CreditCard
     */
    public $card;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new OffsiteGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantID('Billy');
        $this->gateway->setSecretKey('really_secure');
        $this->card = new CreditCard(array('email' => 'mail@mail.com'));
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase(array('testMode' => TRUE, 'amount' => '10.00', 'currency' => 978, 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit-mercanet.test.sips-atos.com/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    public function testAuthorize()
    {
        $response = $this->gateway->authorize(array('amount' => '10.00', 'currency' => 978, 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit.mercanet.bnpparibas.net/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    public function testCapture()
    {
        $response = $this->gateway->capture(array('amount' => '10.00', 'currency' => 978, 'card' => $this->card, 'testMode' => true))->send();
        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://payment-webinit-mercanet.test.sips-atos.com/paymentInit', $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00',));

        $this->assertInstanceOf('Omnipay\Mercanet\Message\OffsiteCompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    /**
     * @expectedException Omnipay\Common\Exception\InvalidRequestException
     */
    public function testCompletePurchaseSendMissingEmail()
    {
        $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'USD', 'card' => array(
            'firstName' => 'Pokemon',
            'lastName' => 'The second',
        )))->send();
    }
}
