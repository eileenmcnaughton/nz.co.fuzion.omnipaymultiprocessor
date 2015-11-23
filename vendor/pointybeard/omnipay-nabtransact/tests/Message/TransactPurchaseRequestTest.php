<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Omnipay;

class TransactPurchaseRequestTest extends TestCase
{
  /**
   * @var TransactPurchaseRequest
   */
  private $request;

  protected function setUp()
  {
    $this->request = new TransactPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    $this->request->initialize(array(
      'merchantId' => 'XYZ0010',
      'password' => 'abcd1234',
      'transactionId' => 'test',
      'currency' => 'AUD',
      'description' => 23,
      'amount' => '2.00',
      'card' => array(
        'firstName' => 'John',
        'lastName' => 'Smith',
        'number' => '4444333322221111',
        'expiryYear' => 2016,
        'expiryMonth' => 8,
      )
    ));
  }

  /**
   * @dataProvider dataProvider
   */
  public function testGetData($xml)
  {
    $this->request->setMessageTimestamp('20141305111214383000+660');
    $this->request->setMessageID('8af793f9af34bea0cf40f5fb750f64');
    $data = $this->request->getData();
    $this->assertInstanceOf('SimpleXMLElement', $data);

    // Just so the provider remains readable...
    $dom = dom_import_simplexml($data)->ownerDocument;
    $dom->formatOutput = true;

    $this->assertEquals($xml, $dom->saveXML());
  }

  public function testSendData() {
    $gateWay = $this->gateway = Omnipay::create('NABTransact_Transact');
    $this->request->send();
  }

  public function dataProvider()
  {
    $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<NABTransactMessage>
  <RequestType>Payment</RequestType>
  <MessageInfo>
    <messageID>8af793f9af34bea0cf40f5fb750f64</messageID>
    <timeoutValue>60</timeoutValue>
    <apiVersion>xml-4.2</apiVersion>
    <messageTimestamp>20141305111214383000+660</messageTimestamp>
  </MessageInfo>
  <MerchantInfo>
    <merchantID>XYZ0010</merchantID>
    <password>abcd1234</password>
  </MerchantInfo>
  <Payment>
    <TxnList count="1">
      <Txn ID="1">
        <txnType>0</txnType>
        <txnSource>23</txnSource>
        <amount>200</amount>
        <currency>AUD</currency>
        <purchaseOrderNo>test</purchaseOrderNo>
        <CreditCardInfo>
          <cardNumber>4444333322221111</cardNumber>
          <expiryDate>08/16</expiryDate>
          <cardHolderName>John Smith</cardHolderName>
          <recurringfag>no</recurringfag>
        </CreditCardInfo>
      </Txn>
    </TxnList>
  </Payment>
</NABTransactMessage>

EOF;

    return array(
      array($xml),
    );
  }
}
