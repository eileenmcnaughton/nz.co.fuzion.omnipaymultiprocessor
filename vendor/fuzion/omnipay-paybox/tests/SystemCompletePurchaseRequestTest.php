<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Tests\TestCase;

class SystemCompletePurchaseRequestTest extends TestCase
{
    /**
     * Request object.
     *
     * @var SystemCompletePurchaseRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new SystemCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrectly signed response
     */
    public function testGetDataInvalid()
    {
        $this->getHttpRequest()->request->replace(array('x_MD5_Hash' => 'invalid'));
        $this->request->getData();
    }

    public function testSendFailed()
    {
        $signature = 'opPlzAadVvCor99yZ8oj2NHmE0eAxXkmCZ80C%2BYW8htpF7Wf6krYYFjc1pQnvYHcW7vp3ta3p8Gfh7gAaR6WDOnhe1Xzm39whk11%2BShieXbQCnEKXot4aGkpodxi1cHutXBhh1IBQOLgq1IVM%2BaV9PUeTI%2FGFruSDnA1TExDHZE%3D';
        $this->getHttpRequest()->request->replace(
            array(
                'Mt' => 100,
                'Id' => 45,
                'Erreur' => '00114',
                'sign' => $signature,
                )
        );

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(45, $response->getTransactionReference());
        $this->assertSame('Transaction failed', $response->getMessage());
    }

    /**
     * This is an example of the data sent to a callback url.
     *
     * From the manual:
     *
     * With the IPN URL parameter (PBX_REPONDRE_A), the signature is only calculated on the content of the
     * PBX_RETOUR parameter, while for the 3 other callback URLs, the signature is calculated on the entire
     * content of the URL"
     */
    public function testSendFailedInteractiveURL()
    {
        $signature = 'LdQOkcHeDtRqI1nNE2muQhXI4VX%2Bt6hocc8zvc7hNgXX%2BKYaY20qBPQoHNFvxHe39PT9AaK8zGHxhP0b9snvuCwJA5Uum2jK1z4qYV796glg44WpmU4gD5cbvgb7jlrbynhui0JiDUTv0a0Icd%2BVAkRvsPwNd1wmjcmOncNzdxY%3D';
        $this->getHttpRequest()->request->replace(
            array(
                '_qf_ThankYou_display' => 1,
                'qfKey' => '186ef69ddf7e1d85665dbb16947d699c_159',
                'Mt' => 100,
                'Id' => 45,
                'Erreur' => '00114',
                'sign' => $signature,
            )
        );

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(45, $response->getTransactionReference());
        $this->assertSame('Transaction failed', $response->getMessage());
    }

    public function testSend()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'Mt' => 100,
                'Id' => 43,
                'Ref' => 'XXXXXX',
                'Erreur' => '00000',
                'sign' => 'K4NWXUX4i+m210aO8dv/6kukA0sNJIR4LjGHTpBrv06Gg/3HtSqJHirA8QbyBpiiEm82NuCANR/ZicWOa9OXRrCY3w7GAD2x9kPe/TPUadDDiBxnJK/8Dcqoum0LII3+jIUD+6c+ZB6jf8h+4xw11DBdvY2sJYddnFoUwYnEEfY=',
            )
        );

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame(43, $response->getTransactionReference());

    }
}
