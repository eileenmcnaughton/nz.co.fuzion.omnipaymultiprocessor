<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Tests\TestCase;

class TransferResponseTest extends TestCase
{
    public function testTransferProcessed()
    {
        $httpResponse = $this->getMockHttpResponse('TransferProcessed.txt');
        $response = new TransferResponse($this->getMockRequest(), $httpResponse->xml());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(20.12, $response->getAmount());
        $this->assertSame('SEK', $response->getCurrency());
        $this->assertSame('1046551278', $response->getTransactionReference());
        $this->assertSame(TransferResponse::STATUS_PROCESSED, $response->getStatus());
        $this->assertSame('processed', $response->getStatusMessage());

        $this->assertSame($response->getStatus(), $response->getCode());
        $this->assertSame($response->getStatusMessage(), $response->getMessage());
    }
}
