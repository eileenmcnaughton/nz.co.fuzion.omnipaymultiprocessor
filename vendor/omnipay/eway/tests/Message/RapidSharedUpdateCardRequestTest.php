<?php

namespace Omnipay\Eway\Message;

use Omnipay\Tests\TestCase;

class RapidSharedUpdateCardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RapidSharedUpdateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'apiKey' => 'my api key',
            'password' => 'secret',
            'returnUrl' => 'https://www.example.com/return',
            'cardReference' => '123456789'
        ));
    }

    public function testGetData()
    {
        $this->request->initialize(array(
            'apiKey' => 'my api key',
            'password' => 'secret',
            'returnUrl' => 'https://www.example.com/return',
            'cardReference' => '123456789',
            'card' => array(
                'title' => 'Mr',
                'firstName' => 'Patrick',
                'lastName' => 'Collison',
                'country' => 'AU',
            ),
        ));

        $data = $this->request->getData();

        $this->assertSame('Purchase', $data['TransactionType']);
        $this->assertSame('UpdateTokenCustomer', $data['Method']);
        $this->assertSame('https://www.example.com/return', $data['RedirectUrl']);
        $this->assertSame(0, $data['Payment']['TotalAmount']);
        $this->assertSame('Mr', $data['Customer']['Title']);
        $this->assertSame('Patrick', $data['Customer']['FirstName']);
        $this->assertSame('Collison', $data['Customer']['LastName']);
        $this->assertSame('au', $data['ShippingAddress']['Country']);
        $this->assertSame('123456789', $data['Customer']['TokenCustomerID']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RapidSharedUpdateCardRequestSuccess.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('https://secure.ewaypayments.com/sharedpayment?AccessCode=F9802j0-O7sdVLnOcb_3IPryTxHDtKY8u_0pb10GbYq-Xjvbc-5Bc_LhI-oBIrTxTCjhOFn7Mq-CwpkLDja5-iu-Dr3DjVTr9u4yxSB5BckdbJqSA4WWydzDO0jnPWfBdKcWL', $response->getRedirectUrl());
        $this->assertNull($response->getRedirectData());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertNotNull($response->getCardReference());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('RapidSharedUpdateCardRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getRedirectData());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('V6040', $response->getCode());
    }

    public function testCancelUrl()
    {
        $this->assertSame($this->request, $this->request->setCancelUrl('http://www.example.com'));
        $this->assertSame('http://www.example.com', $this->request->getCancelUrl());
    }

    public function testLogoUrl()
    {
        $this->assertSame($this->request, $this->request->setLogoUrl('https://www.example.com/logo.jpg'));
        $this->assertSame('https://www.example.com/logo.jpg', $this->request->getLogoUrl());
    }

    public function testHeaderText()
    {
        $this->assertSame($this->request, $this->request->setHeaderText('Header Text'));
        $this->assertSame('Header Text', $this->request->getHeaderText());
    }

    public function testLanguage()
    {
        $this->assertSame($this->request, $this->request->setLanguage('EN'));
        $this->assertSame('EN', $this->request->getLanguage());
    }

    public function testCustomerReadOnly()
    {
        $this->assertSame($this->request, $this->request->setCustomerReadOnly('true'));
        $this->assertSame('true', $this->request->getCustomerReadOnly());
    }

    public function testCustomView()
    {
        $this->assertSame($this->request, $this->request->setCustomView('Bootstrap'));
        $this->assertSame('Bootstrap', $this->request->getCustomView());
    }

    public function testVerifyCustomerPhone()
    {
        $this->assertSame($this->request, $this->request->setVerifyCustomerPhone('true'));
        $this->assertSame('true', $this->request->getVerifyCustomerPhone());
    }

    public function testVerifyCustomerEmail()
    {
        $this->assertSame($this->request, $this->request->setVerifyCustomerEmail('true'));
        $this->assertSame('true', $this->request->getVerifyCustomerEmail());
    }

}
