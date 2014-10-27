<?php

namespace Omnipay\Paybox;

use Omnipay\Tests\GatewayTestCase;

class SystemGatewayTest extends GatewayTestCase
{
  /**
   * @var SystemGateway
   */
    public $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new SystemGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Paybox\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Paybox\Message\CompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchaseSend()
    {
      $request = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'USD', 'card' => array(
        'firstName' => 'Pokemon',
        'lastName' => 'The second',
      )))->send();

      $this->assertInstanceOf('Omnipay\Paybox\Message\Response', $request);
      $this->assertTrue($request->isTransparentRedirect());
    }
}
