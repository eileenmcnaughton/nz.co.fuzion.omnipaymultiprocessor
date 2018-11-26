<?php

namespace Omnipay\Razorpay\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Razorpay\Message\PurchaseRequest;
use Omnipay\Razorpay\Message\PurchaseResponse;

class PurchaseResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->parameters = [
            'amount'     => '10.00',
            'currency'   => 'INR',
            'card'       => $this->getValidCard(),
            'key_id'     => 'random_key_id',
            'key_secret' => 'random_key_secret',
        ];

        $this->request->initialize($this->parameters);

        $this->data = $this->request->getData();

        $this->response = new PurchaseResponse($this->request, $this->data);
    }

    public function testGetRedirectFlow()
    {
        $url = $this->response->getRedirectUrl();
        $this->assertSame('https://checkout.razorpay.com/integration/shopify', $url);

        $isRedirect = $this->response->isRedirect();
        $this->assertSame($isRedirect, true);

        $method = $this->response->getRedirectMethod();
        $this->assertSame($method, 'POST');
    }

    public function testGetRedirectData()
    {
        $data = $this->response->getRedirectData();

        $this->assertSame($this->data, $data);
    }
}
