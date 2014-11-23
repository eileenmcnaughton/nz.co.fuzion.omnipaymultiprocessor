<?php

namespace Omnipay\Gopay\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->soapClient = $this->getMockFromWsdl(__DIR__ . '/../EPaymentServiceV2.wsdl');

        $this->request = new PurchaseRequest($this->soapClient, $this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetDataWithCard()
    {
        $this->request->initialize(array(
            'goId' => '1234',
            'secureKey' => '0123456789abcdef',
            'amount' => '10.00',
            'currency' => 'CZK',
            'description' => 'Product Description',
            'returnUrl' => 'https://www.example.com/return',
            'cancelUrl' => 'https://www.example.com/cancel',
            'transactionId' => '98765',
            'card' => new CreditCard(array(
                'name' => 'John Doe',
                'address1' => '123 NW Blvd',
                'address2' => 'Lynx Lane',
                'city' => 'Topeka',
                'state' => 'KS',
                'country' => 'USA',
                'postcode' => '66605',
                'phone' => '555-555-5555',
                'email' => 'test@email.com',
            ))
        ));

        $expected = array(
            'targetGoId' => '1234',
            'productName' => 'Product Description',
            'totalPrice' => '1000',
            'currency' => 'CZK',
            'orderNumber' => '98765',
            'failedURL' => 'https://www.example.com/cancel',
            'successURL' => 'https://www.example.com/return',
            'preAuthorization' => '',
            'recurrentPayment' => '',
            'recurrenceDateTo' => '',
            'recurrenceCycle' => '',
            'recurrencePeriod' => '',
            'paymentChannels' => '',
            'defaultPaymentChannel' => '',
            'encryptedSignature' => 'WILL BE SET LATER',
            'customerData' => array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'city' => 'Topeka',
                'street' => '123 NW Blvd',
                'postalCode' => '66605',
                'countryCode' => '',
                'email' => 'test@email.com',
                'phoneNumber' => '555-555-5555'
            ),

            'p1' => '',
            'p2' => '',
            'p3' => '',
            'p4' => '',
            'lang' => ''
        );

        $data = $this->request->getData();
        $this->assertNotNull($data['encryptedSignature']);

        $expected['encryptedSignature'] = $data['encryptedSignature'];
        $this->assertEquals($expected, $data);
    }
}
