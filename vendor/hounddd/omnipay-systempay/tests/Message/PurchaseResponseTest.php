<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new PurchaseRequest( $this->getHttpClient(), $this->getHttpRequest() );
        $this->request->initialize( array(
            'merchantId' => '12345678',
            'transactionId' => '654321',
            'amount' => '15.24',
            'currency' => 'EUR',
            'testMode' => true,
            'transactionDate' => '20090501193530',
            'certificate' => '1122334455667788'
        ) );
        $this->response = $this->request->send();
    }

    public function testResponse()
    {
        $this->assertTrue( $this->response->isRedirect() );
        $this->assertSame( 'https://paiement.systempay.fr/vads-payment/', $this->response->getRedirectUrl() );
        $this->assertSame( 'POST', $this->response->getRedirectMethod() );
    }
}
