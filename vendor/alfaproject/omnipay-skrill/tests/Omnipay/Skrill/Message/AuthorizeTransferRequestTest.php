<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Tests\TestCase;

class AuthorizeTransferRequestTest extends TestCase
{
    /**
     * @var AuthorizeTransferRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new AuthorizeTransferRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $expectedData = array(
            'email'         => 'test@php.unit',
            'password'      => 'password',
            'amount'        => '12.34',
            'currency'      => 'EUR',
            'subject'       => 'subject',
            'note'          => 'note',
            'customerEmail' => 'customer@php.unit',
            'transactionId' => 'transaction_id',
        );
        $this->request->initialize($expectedData);

        $data = $this->request->getData();

        $this->assertSame('prepare', $data['action']);
        $this->assertSame($expectedData['email'], $data['email']);
        $this->assertSame($expectedData['password'], $data['password']);
        $this->assertSame($expectedData['amount'], $data['amount']);
        $this->assertSame($expectedData['currency'], $data['currency']);
        $this->assertSame($expectedData['subject'], $data['subject']);
        $this->assertSame($expectedData['note'], $data['note']);
        $this->assertSame($expectedData['customerEmail'], $data['bnf_email']);
        $this->assertSame($expectedData['transactionId'], $data['frn_trn_id']);
    }
}
