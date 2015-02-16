<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Tests\TestCase;

class SystemCompleteAuthorizeResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = new SystemCompleteAuthorizeResponse(
            $this->getMockRequest(),
            array(
               'Erreur' => '00000',
               'Id' => '12345',
                )
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testFailure()
    {
        $response = new SystemCompleteAuthorizeResponse($this->getMockRequest(), array('x_response_code' => '0', 'x_response_reason_text' => 'Declined'));

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('Transaction failed', $response->getMessage());
    }
}
