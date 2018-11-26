<?php

namespace Omnipay\Razorpay\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Razorpay\Message\Signature;
use Omnipay\Razorpay\Message\PurchaseRequest;

class PurchaseRequestTest extends TestCase
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
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $parameters = [
            'x_amount'     => $this->parameters['amount'],
            'x_currency'   => $this->parameters['currency'],
            'x_account_id' => $this->parameters['key_id'],
        ];

        // assert array was created correctly
        $this->assertSame($this->parameters['amount'], $data['x_amount']);
        $this->assertSame($this->parameters['currency'], $data['x_currency']);
        $this->assertSame($this->parameters['key_id'], $data['x_account_id']);

        // assert signature was created correctly
        $razorpaySignature = new Signature($this->parameters['key_secret']);
        $signature = $razorpaySignature->getSignature($data);

        $this->assertSame($data['x_signature'], $signature);
    }

    // If card details are empty, we return default parameters
    public function testDefaultGetData()
    {
        $parameters = [
            'amount'     => '10.00',
            'currency'   => 'INR',
            'key_id'     => 'random_key_id',
            'key_secret' => 'random_key_secret',
        ];

        $this->request->initialize($parameters);

        $data = $this->request->getData();

        $this->assertSame($data, $data);
    }

    public function testSendData()
    {
        $response = $this->request->send();

        $this->assertInstanceOf('\Omnipay\Razorpay\Message\PurchaseResponse', $response);
    }
}
