<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * The CompleteRequest object is invoked in the callback handler.
 */

use Omnipay\Tests\TestCase;

class DPMCompleteRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DPMCompleteRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect hash
     */
    public function testGetDataInvalid()
    {
        $this->getHttpRequest()->request->replace(array('x_MD5_Hash' => 'invalid'));
        $this->request->getData();
    }

    public function testGetMd5Hash()
    {
        $this->assertSame(md5(''), $this->request->getHash());

        $this->request->setHashSecret('hashsec');
        $this->request->setApiLoginId('apilogin');

        $this->getHttpRequest()->request->replace(
            array(
                'x_trans_id' => 'trnid',
                'x_amount' => '10.00',
            )
        );

        $this->assertSame(
            md5('hashsec' . 'apilogin' . 'trnid' . '10.00'),
            $this->request->getHash()
        );
    }

    public function testGetSha512Hash()
    {
        $this->request->setSignatureKey('48D2C629E4AE7CA3C4E6CD7223DA');

        $this->getHttpRequest()->request->replace(
            array(
                'x_trans_id' => 'trn123456',
                'x_test_request' => 'xxx',
                'x_response_code' => 'xxx',
                'x_auth_code' => 'xxx',
                'x_cvv2_resp_code' => 'xxx',
                'x_cavv_response' => 'xxx',
                'x_avs_code' => 'xxx',
                'x_method' => 'xxx',
                'x_account_number' => 'xxx',
                'x_amount' => '10.99',
                'x_company' => 'xxx',
                'x_first_name' => 'xxx',
                'x_last_name' => 'xxx',
                'x_address' => 'xxx',
                'x_city' => 'xxx',
                'x_state' => 'xxx',
                'x_zip' => 'xxx',
                'x_country' => 'xxx',
                'x_phone' => 'xxx',
                'x_fax' => 'xxx',
                'x_email' => 'xxx',
                'x_ship_to_company' => 'xxx',
                'x_ship_to_first_name' => 'xxx',
                'x_ship_to_last_name' => 'xxx',
                'x_ship_to_address' => 'xxx',
                'x_ship_to_city' => 'xxx',
                'x_ship_to_state' => 'xxx',
                'x_ship_to_zip' => 'xxx',
                'x_ship_to_country' => 'xxx',
                'x_invoice_num' => 'xxx',
            )
        );

        $this->assertSame(
            'F9A0DE7A9AC83E0B8043CD7CBD804ED41FE6BFDDB2C10C486DB4E3C4F3E7163237837A5CD6AEE1FAFF03BAD076DF287F7E81E17ED38752999D1AA6249ECC1613',
            $this->request->getHash()
        );
    }

    public function testSend()
    {
        // The hash contains no data supplied by the merchant site, apart
        // from the secret. This is the first point at which we see the transaction
        // reference (x_trans_id), and this hash is to validate that the reference and
        // the amount have not be tampered with en-route.

        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => strtolower(md5('shhh' . 'user' . '12345' . '10.00')),
                'omnipay_transaction_id' => '99',
            )
        );
        $this->request->setApiLoginId('user');
        $this->request->setHashSecret('shhh');

        $this->request->setAmount('10.00');

        $this->request->setReturnUrl('http://example.com/');

        // Issue #22 Transaction ID in request is picked up from custom field.
        $this->assertSame('99', $this->request->getTransactionId());

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertSame(true, $response->isRedirect());
        // CHECKME: does it matter what letter case the method is?
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('http://example.com/', $response->getRedirectUrl());
        $this->assertNull($response->getMessage());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect amount
     */
    public function testSendWrongAmount()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'x_response_code' => '1',
                'x_trans_id' => '12345',
                'x_amount' => '10.00',
                'x_MD5_Hash' => strtolower(md5('shhh' . 'user' . '12345' . '10.00')),
            )
        );
        $this->request->setApiLoginId('user');
        $this->request->setHashSecret('shhh');

        // In the notify, the merchant application sets the amount that
        // was expected to be authorised. We expected 20.00 but are being
        // told it was 10.00.

        $this->request->setAmount('20.00');

        $response = $this->request->send();
    }
}