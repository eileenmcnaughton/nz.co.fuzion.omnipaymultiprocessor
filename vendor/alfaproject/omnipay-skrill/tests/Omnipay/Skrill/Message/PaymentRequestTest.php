<?php

namespace Omnipay\Skrill\Message;

use DateTime;
use Omnipay\Tests\TestCase;

class PaymentRequestTest extends TestCase
{
    /**
     * @var PaymentRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new PaymentRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $expectedData = array(
            'email' => 'test@php.unit',
            'language' => 'EN',
            'amount' => '12.34',
            'currency' => 'EUR',
            'details' => array('key' => 'value'),
            'recipientDescription' => 'phpunit',
            'transactionId' => 'ref',
            'returnUrl' => 'http://php.unit/return',
            'returnUrlText' => 'return',
            'returnUrlTarget' => 3,
            'cancelUrl' => 'http://php.unit/cancel',
            'cancelUrlTarget' => 3,
            'notifyUrl' => 'http://php.unit/status',
            'notifyUrl2' => 'http://php.unit/status2',
            'newWindowRedirect' => 0,
            'hideLogin' => false,
            'confirmationNote' => 'confirmation note',
            'logoUrl' => 'http://php.unit/logo.png',
            'referralId' => 'ref_id',
            'extReferralId' => 'ext_ref_id',
            'merchantFields' => array('key' => 'value'),
            'customerEmail' => 'customer@php.unit',
            'customerTitle' => 'Mr',
            'customerFirstName' => 'php',
            'customerLastName' => 'unit',
            'customerBirthday' => new DateTime('2014-01-03'),
            'customerAddress1' => 'address1',
            'customerAddress2' => 'address2',
            'customerPhone' => '987654321',
            'customerPostalCode' => 'zip',
            'customerCity' => 'city',
            'customerState' => 'state',
            'customerCountry' => 'XXX',
            'amountDescriptions' => array('key' => 'value'),
        );
        $this->request->initialize($expectedData);
        $this->request->setPaymentMethod(PaymentMethod::SKRILL_DIRECT);

        $data = $this->request->getData();

        $this->assertSame($expectedData['email'], $data['pay_to_email']);
        $this->assertSame($expectedData['language'], $data['language']);
        $this->assertSame($expectedData['amount'], $data['amount']);
        $this->assertSame($expectedData['currency'], $data['currency']);

        $this->assertSame('key', $data['detail1_description']);
        $this->assertSame('value', $data['detail1_text']);

        $this->assertSame($expectedData['recipientDescription'], $data['recipient_description']);
        $this->assertSame($expectedData['transactionId'], $data['transaction_id']);
        $this->assertSame($expectedData['returnUrl'], $data['return_url']);
        $this->assertSame($expectedData['returnUrlText'], $data['return_url_text']);
        $this->assertSame($expectedData['returnUrlTarget'], $data['return_url_target']);
        $this->assertSame($expectedData['cancelUrl'], $data['cancel_url']);
        $this->assertSame($expectedData['cancelUrlTarget'], $data['cancel_url_target']);
        $this->assertSame($expectedData['notifyUrl'], $data['status_url']);
        $this->assertSame($expectedData['notifyUrl2'], $data['status_url2']);
        $this->assertSame($expectedData['newWindowRedirect'], $data['new_window_redirect']);
        $this->assertSame(0, $data['hide_login']);
        $this->assertSame($expectedData['confirmationNote'], $data['confirmation_note']);
        $this->assertSame($expectedData['logoUrl'], $data['logo_url']);
        $this->assertSame(1, $data['prepare_only']);
        $this->assertSame($expectedData['referralId'], $data['rid']);
        $this->assertSame($expectedData['extReferralId'], $data['ext_ref_id']);

        $this->assertSame('key', $data['merchant_fields']);
        $this->assertSame('value', $data['key']);

        $this->assertSame($expectedData['customerEmail'], $data['pay_from_email']);
        $this->assertSame($expectedData['customerTitle'], $data['title']);
        $this->assertSame($expectedData['customerFirstName'], $data['firstname']);
        $this->assertSame($expectedData['customerLastName'], $data['lastname']);
        $this->assertSame($expectedData['customerBirthday']->format('dmY'), $data['date_of_birth']);
        $this->assertSame($expectedData['customerAddress1'], $data['address']);
        $this->assertSame($expectedData['customerAddress2'], $data['address2']);
        $this->assertSame($expectedData['customerPhone'], $data['phone_number']);
        $this->assertSame($expectedData['customerPostalCode'], $data['postal_code']);
        $this->assertSame($expectedData['customerCity'], $data['city']);
        $this->assertSame($expectedData['customerState'], $data['state']);
        $this->assertSame($expectedData['customerCountry'], $data['country']);

        $this->assertSame('key', $data['amount2_description']);
        $this->assertSame('value', $data['amount2']);

        $this->assertSame(PaymentMethod::SKRILL_DIRECT, $data['payment_methods']);
    }
}
