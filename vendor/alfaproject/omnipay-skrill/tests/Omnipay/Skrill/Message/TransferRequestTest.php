<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Tests\TestCase;

class TransferRequestTest extends TestCase
{
    /**
     * @var AuthorizeTransferRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new TransferRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $expectedData = array(
            'sessionId' => 'session_id',
        );
        $this->request->initialize($expectedData);

        $data = $this->request->getData();

        $this->assertSame('transfer', $data['action']);
        $this->assertSame($expectedData['sessionId'], $data['sid']);
    }
}
