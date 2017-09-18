<?php

namespace Omnipay\Skrill\Message;

class StatusClassCallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testCalculateMd5Signature()
    {
        $callback = new StatusCallback([
            'transaction_id' => 'transaction',
            'mb_amount' => '1',
            'amount' => '1',
            'mb_transaction_id' => '2155172570',
            'mb_currency' => 'EUR',
            'pay_from_email' => 'skrilltest@mail.com',
            'md5sig' => '8BF49E907E28F2286656FA7342D94590',
            'pay_to_email' => 'skrilltest@mail.com',
            'currency' => 'EUR',
            'merchant_id' => '32220024',
            'customer_id' => '32220084',
            'status' => '2'
        ]);
        $callback->setSecretWord(md5('random'));

        $this->assertEquals('5630F908E45A6CF04EC615B6A0DED988', $callback->calculateMd5Signature());
    }

    public function testTestMdSignaturesDisabledDueToNoSecretWord()
    {
        $callback = new StatusCallback([
            'transaction_id' => 'transaction',
            'mb_amount' => '1',
            'amount' => '1',
            'mb_transaction_id' => '2155172570',
            'mb_currency' => 'EUR',
            'pay_from_email' => 'skrilltest@mail.com',
            'md5sig' => '8BF49E907E28F2286656FA7342D94590',
            'pay_to_email' => 'skrilltest@mail.com',
            'currency' => 'EUR',
            'merchant_id' => '32220024',
            'customer_id' => '32220084',
            'status' => '2'
        ]);

        $this->assertTrue($callback->testMdSignatures());
    }
}
