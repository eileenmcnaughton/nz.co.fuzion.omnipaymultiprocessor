<?php

namespace Omnipay\Cybersource\Message;

use Mockery as m;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'currency' => 'AUD',
                'amount' => '12.00',
                'card' => $this->getValidCard(),
            )
        );
    }

    public function testGetRequiredFields()
    {
        $fields = $this->request->getRequiredFields();
        $this->assertContains('firstName', $fields);
        $this->assertNotContains('billingState', $fields);
    }

    public function testGetRequiredFieldsCanada()
    {
        $this->request->setIsUsOrCanada(TRUE);
        $fields = $this->request->getRequiredFields();
        $this->assertContains('postcode', $fields);
    }

    /**
     * @expectedException Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The currency parameter is required
     */
    public function testGetDataMissingFields()
    {
        $this->request->setCurrency(NULL);
        $this->request->getData();
    }

    public function testGetCardType()
    {
        $this->assertEquals('001', $this->request->getCardType());
    }

    public function testGetData() {
        $this->request->getData();
    }
}

