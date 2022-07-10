<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
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
            'certificate' => '1122334455667788',
            'orderId' => '123',
            'successUrl' => 'http://success',
            'cancelUrl' => 'http://cancel',
            'errorUrl' => 'http://error',
            'refusedUrl' => 'http://refused',
            'notifyUrl' => 'http://notify',
        ) );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame( 1524, $data['vads_amount'] );
        $this->assertSame( '12345678', $data['vads_site_id'] );
        $this->assertSame( '978', $data['vads_currency'] );
        $this->assertSame( 'INTERACTIVE', $data['vads_action_mode'] );
        $this->assertSame( 'TEST', $data['vads_ctx_mode'] );
        $this->assertSame( 'PAYMENT', $data['vads_page_action'] );
        $this->assertSame( 'SINGLE', $data['vads_payment_config'] );
        $this->assertSame( 'V2', $data['vads_version'] );
        $this->assertSame( '654321', $data['vads_trans_id'] );
        $this->assertSame( '20090501193530', $data['vads_trans_date'] );
        $this->assertSame( '123', $data['vads_order_id'] );
        $this->assertSame( 'http://success', $data['vads_url_success'] );
        $this->assertSame( 'http://cancel', $data['vads_url_cancel'] );
        $this->assertSame( 'http://error', $data['vads_url_error'] );
        $this->assertSame( 'http://refused', $data['vads_url_refused'] );
        $this->assertSame( '1e44c55b059db1b4d1b704d0bcede1fc273add9b', $data['signature'] );
    }
}
