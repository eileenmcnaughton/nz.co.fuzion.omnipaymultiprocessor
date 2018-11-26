<?php

namespace Omnipay\Razorpay\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Razorpay\Message\Signature;
use Omnipay\Razorpay\Message\CompletePurchaseRequest;
use Omnipay\Razorpay\Message\CompletePurchaseResponse;

class CompletePurchaseResponseTest extends TestCase
{
    public function setUp()
    {
        $request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $parameters = [
            'amount'     => '10.00',
            'currency'   => 'INR',
            'card'       => $this->getValidCard(),
            'key_id'     => 'random_key_id',
            'key_secret' => 'random_key_secret',
        ];

        $request->initialize($parameters);

        $this->data = $request->getData();
        $this->data['key_secret'] = $parameters['key_secret'];

        $this->response = new CompletePurchaseResponse($request, $this->data);
    }

    public function testIsSuccessful()
    {
        $_POST = $this->data;
        $_POST['x_result'] = 1;

        $keySecret = $_POST['key_secret'];
        unset($_POST['key_secret']);

        $verifySignature = new Signature($keySecret);
        $_POST['x_signature'] = $verifySignature->getSignature($_POST);

        $success = $this->response->isSuccessful();

        $this->assertEquals($success, true);
    }
}
