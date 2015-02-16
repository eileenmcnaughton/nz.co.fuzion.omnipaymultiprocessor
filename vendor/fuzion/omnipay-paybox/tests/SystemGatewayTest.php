<?php

namespace Omnipay\Paybox;

use Omnipay\Tests\GatewayTestCase;

class SystemGatewayTest extends GatewayTestCase
{
    /**
     * Key for test site - see http://www1.paybox.com/wp-content/uploads/2014/02/PayboxTestParameters_V6.2_EN.pdf
     * @var string
     */
    public $key = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';

    /**
     * @var SystemGateway
     */
    public $gateway;

    /**
     * Test credentials site number.
     *
     * @var int
     */
    public $site = 1999888;

    /**
     * Test credentials RANG.
     *
     * @var int
     */
    public $rang = 32;

    public $identifiant = '107904482';

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new SystemGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Paybox\Message\SystemPurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi', $request->getEndpoint());
    }

    public function testPurchaseTestMode()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00', 'testMode' => true));

        $this->assertInstanceOf('Omnipay\Paybox\Message\SystemPurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi', $request->getEndpoint());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Paybox\Message\SystemCompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchaseSend()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'USD', 'card' => array(
            'firstName' => 'Pokemon',
            'lastName' => 'The second',
            'email' => 'test@paybox.com',
        )))->send();

        $this->assertInstanceOf('Omnipay\Paybox\Message\SystemResponse', $request);
        $this->assertFalse($request->isTransparentRedirect());
    }


    public function testCompletePurchaseSendWithSiteData()
    {
        $gateway = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'EUR', 'card' => array(
            'firstName' => 'Pokemon',
            'lastName' => 'The second',
            'email' => 'test@paybox.com',
        )));

        $gateway->setRang($this->rang);
        $gateway->setSite($this->site);
        $gateway->setIdentifiant($this->identifiant);
        $gateway->setTransactionID(3);
        $gateway->setTime("2014-12-09T22:37:34+00:00");
        $gateway->setKey($this->key);
        $request = $gateway->send();

        $this->assertInstanceOf('Omnipay\Paybox\Message\SystemResponse', $request);
        $this->assertFalse($request->isTransparentRedirect());
        $expected_url = "https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi?PBX_SITE=1999888&PBX_RANG=32";
        $expected_url .= "&PBX_IDENTIFIANT=107904482&PBX_TOTAL=1000&PBX_DEVISE=978&PBX_CMD=3&PBX_PORTEUR=test%40paybox.com";
        $expected_url .= "&PBX_RETOUR=Mt%3AM%3BId%3AR%3BRef%3AA%3BErreur%3AE%3Bsign%3AK&PBX_TIME=2014-12-09T22%3A37%3A34%2B00%3A00";
        $hmac = '309CF65B9A4381B44DAC7D8979208FCC4E0F3E819A00E1C5602B419FD7E4C2389468E679F30522581752B8FD26A8816004B6A17EFCDD2BEAD8F16A26D092EA98';
        $expected_url .= "&PBX_HMAC=" . $hmac;
        $this->assertSame($expected_url, $request->getRedirectUrl());
    }
}
