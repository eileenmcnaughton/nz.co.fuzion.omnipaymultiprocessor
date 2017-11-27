<?php
namespace Omnipay\pergo;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\pergo\OffsiteGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\CreditCard;

class OffsiteGatewayTest extends GatewayTestCase
{
  /**
   * @var Omnipay/pergo/SystemGateway
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
        $this->gateway->setAuthenticationToken('Billy');
        $this->gateway->setBillerAccountId('really_secure');
        $this->card = new CreditCard(array('email' => 'mail@mail.com'));
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 978, 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\pergo\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://protectpaytest.propay.com/hpp/v2/[hostedtransactionidentifier?type=sale&authenticationtoken=Billy&billeraccountid=really_secure&total=10.00', $response->getRedirectUrl());
        $this->assertFalse($response->isTransparentRedirect());
    }

    public function testAuthorize()
    {
        $response = $this->gateway->authorize(array('amount' => '10.00', 'currency' => 978, 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\pergo\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://protectpaytest.propay.com/hpp/v2/[hostedtransactionidentifier?type=Authorize&authenticationtoken=Billy&billeraccountid=really_secure&total=10.00', $response->getRedirectUrl());
        $this->assertFalse($response->isTransparentRedirect());
    }

    public function testCapture()
    {
        $response = $this->gateway->capture(array('amount' => '10.00', 'currency' => 978, 'card' => $this->card))->send();
        $this->assertInstanceOf('Omnipay\pergo\Message\OffsiteAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
        $this->assertSame('https://protectpaytest.propay.com/hpp/v2/[hostedtransactionidentifier?type=capture&authenticationtoken=Billy&billeraccountid=really_secure&total=10.00', $response->getRedirectUrl());
        $this->assertFalse($response->isTransparentRedirect());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00',));

        $this->assertInstanceOf('Omnipay\pergo\Message\OffsiteCompletePurchaseRequest', $request);
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
