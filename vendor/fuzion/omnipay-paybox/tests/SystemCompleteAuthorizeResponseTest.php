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
               'Mt' => 100,
               'Id' => 47,
               'Ref' => 601957,
               'Erreur' => '00000',
               'sign' => 'jRk0xXxO6wGwyL9G0K5oj5Xihxbr0s0gxhBkXT7D0k0KAFKywaqy1xd%2BorpeU1hM2Dq5KvDP42byaRzEIx3ymVvAP%2FCtM7jzTXO58tbvsXojPvLGEqz4q9QhrCH%2BOmQL6AfXt6lXImzjTgrKDIvIu6EX%2BNpp5sbcot%2BafOrTVkQ%3D',
                )
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(601957, $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testFailure()
    {
        $response = new SystemCompleteAuthorizeResponse(
            $this->getMockRequest(),
            array(
                'Mt' => 100,
                'Id' => 45,
                'Erreur' => '00114',
                'sign' => 'opPlzAadVvCor99yZ8oj2NHmE0eAxXkmCZ80C%2BYW8htpF7Wf6krYYFjc1pQnvYHcW7vp3ta3p8Gfh7gAaR6WDOnhe1Xzm39whk11%2BShieXbQCnEKXot4aGkpodxi1cHutXBhh1IBQOLgq1IVM%2BaV9PUeTI%2FGFruSDnA1TExDHZE%3D',
            )
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(45, $response->getTransactionId());
        $this->assertSame('Transaction failed', $response->getMessage());
    }
}
