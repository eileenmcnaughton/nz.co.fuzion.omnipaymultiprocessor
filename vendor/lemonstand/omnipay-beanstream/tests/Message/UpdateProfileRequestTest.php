<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Tests\TestCase;

class UpdateProfileRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new UpdateProfileRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize();
    }

    public function testSendSuccess()
    {
        $this->request->setProfileId('9ba60541d32648B1A3581670123dF2Ef');
        $this->setMockHttpResponse('UpdateProfileSuccess.txt');
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame(1, $response->getCode());
        $this->assertSame('Operation Successful', $response->getMessage());
        $this->assertSame('9ba60541d32648B1A3581670123dF2Ef', $response->getCustomerCode());
    }

    public function testSendError()
    {
        $this->request->setProfileId('9ba60541d32648B1A3581670123dF2Ef');
        $this->setMockHttpResponse('UpdateProfileFailure.txt');
        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertSame(15, $response->getCode());
        $this->assertSame(3, $response->getCategory());
        $this->assertSame('Customer code to modify does not exist', $response->getMessage());
    }


    public function testEndpoint()
    {
        $this->assertSame($this->request, $this->request->setProfileId('1'));
        $this->assertSame('1', $this->request->getProfileId());
        $this->assertSame('https://www.beanstream.com/api/v1/profiles/' . $this->request->getProfileId(), $this->request->getEndpoint());
    }

    public function testLanguage()
    {
        $this->assertSame($this->request, $this->request->setLanguage('123'));
        $this->assertSame('123', $this->request->getLanguage());
    }

    public function testComment()
    {
        $this->assertSame($this->request, $this->request->setComment('test'));
        $this->assertSame('test', $this->request->getComment());
    }

    public function testBilling()
    {
        $billing = array(
            'name' => 'test mann',
            'email_address' => 'testmann@email.com',
            'address_line1' => '123 Test St',
            'address_line2' => '',
            'city' => 'vancouver',
            'province' => 'bc',
            'postal_code' => 'H0H0H0',
            'phone_number' => '1 (555) 555-5555'
        );

        $this->assertSame($this->request, $this->request->setBilling($billing));
        $this->assertSame($billing, $this->request->getBilling());
    }

    public function testBillingAndToken()
    {
        $billing = array(
            'name' => 'test mann',
            'email_address' => 'testmann@email.com',
            'address_line1' => '123 Test St',
            'address_line2' => '',
            'city' => 'vancouver',
            'province' => 'bc',
            'postal_code' => 'H0H0H0',
            'phone_number' => '1 (555) 555-5555'
        );

        $token = array(
            'name' => 'token-test-name',
            'code' => 'token-test-code'
        );

        $this->assertSame($this->request, $this->request->setBilling($billing));
        $this->assertSame($this->request, $this->request->setToken($token));
        $this->assertSame($billing, $this->request->getBilling());
        $this->assertSame($token, $this->request->getToken());
    }

    public function testCardAndCardBillingAddress()
    {
        $this->request->setProfileId('9ba60541d32648B1A3581670123dF2Ef');
        $billing1 = array(
            'name' => 'test mann',
            'email_address' => 'testmann@email.com',
            'address_line1' => '123 Test St',
            'address_line2' => '',
            'city' => 'vancouver',
            'province' => 'bc',
            'postal_code' => 'H0H0H0',
            'phone_number' => '1 (555) 555-5555'
        );

        $billing2 = array(
            'name' => 'Example User',
            'address_line1' => '123 Billing St',
            'address_line2' => 'Billsville',
            'city' => 'Billstown',
            'province' => 'CA',
            'country' => 'US',
            'postal_code' => '12345',
            'phone_number' => '(555) 123-4567',
            'email_address' => null
        );

        $card = $this->getValidCard();
        $this->assertSame($this->request, $this->request->setCard($card));
        $this->request->setBilling($billing1);
        $data = $this->request->getData();
        $this->assertNotSame($billing2, $data['billing']);
        $this->assertSame($billing1, $data['billing']);
    }

    public function testHttpMethod()
    {
        $this->assertSame('PUT', $this->request->getHttpMethod());
    }
}
