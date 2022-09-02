<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequestTest extends TestCase
{
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new CompletePurchaseRequest( $this->getHttpClient(), $this->getHttpRequest() );
    }

    /**
     * @expectedException Omnipay\Common\Exception\InvalidResponseException
     */
    public function testGetDataWithCorruptedData_ShouldThrowAnException()
    {
        $data = $this->request->getData();
    }
}
