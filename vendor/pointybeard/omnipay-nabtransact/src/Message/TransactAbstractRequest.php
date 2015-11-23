<?php

/**
 *
 */
namespace Omnipay\NABTransact\Message;
use SimpleXMLElement;

/**
 * Abstract class for Transact gateway.
 */
abstract class TransactAbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    /**
     * Xml object.
     *
     * @var SimpleXMLElement
     */
    protected $xml;

    protected $liveEndpoint = 'https://transact.nab.com.au/live/xmlapi/payment';
    protected $testEndpoint = 'https://transact.nab.com.au/test/xmlapi/payment';

    public function getData()
    {
        return $this->getXmlRequest()->asXML();
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient
          ->post($this->getEndpoint(), null, $this->getData())
          ->send();
        return $this->response = new TransactPurchaseResponse($this, $httpResponse->getBody());
    }

    protected function getXmlRequest()
    {
        $this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><NABTransactMessage/>');
        $this->xml->RequestType = $this->getRequestType();
        $this->addMessageInfoElement();
        $this->addMerchantInfoElement($this->xml);
        $this->addPaymentElement($this->xml);
        $this->setMessageTimestamp(self::generateMessageTimestamp());
        //$this->xml->MessageTimestamp = $this->getMessageTimestamp();
        return $this->xml;
    }

    function addMessageInfoElement() {
        $infoXml = $this->xml->addChild('MessageInfo');
        $infoXml->messageID = $this->getMessageID();
        $infoXml->timeoutValue = 60;
        $infoXml->apiVersion = $this->getApiVersion();
        $infoXml->messageTimestamp = $this->getMessageTimestamp();
    }

    function addMerchantInfoElement(&$xml) {
        $element = $this->xml->addChild('MerchantInfo');
        $element->merchantID = $this->getMerchantId();
        $element->password = $this->getPassword();
        return $element;
    }

    protected function addPaymentElement(&$xml) {
        $topElement = $this->xml->addChild('Payment');
        $trxnList = $topElement->addChild('TxnList');
        $trxnList->addAttribute('count', 1);
        $trxn = $trxnList->addChild('Txn');
        $trxn->addAttribute('ID', 1);
        $trxn->txnType = 0;
        $trxn->txnSource = 23;
        $trxn->amount = $this->getAmountInteger();
        $trxn->currency = $this->getCurrency();
        $trxn->purchaseOrderNo = $this->getTransactionId();
        $this->addCreditCardElement($trxn);
    }

    protected function addCreditCardElement(&$xml) {
        $element = $xml->addChild('CreditCardInfo');
        $card = $this->getCard();
        $element->cardNumber = $card->getNumber();
        $element->cvv = $card->getCvv();
        $element->expiryDate = $card->getExpiryDate('m/y');
        $element->cardHolderName = $card->getName();
        $element->recurringfag = 'no';
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getApiVersion()
    {
        return 'xml-4.2';
    }

    public function getMessageTimestamp()
    {
        return $this->getParameter('messageTimestamp');
    }

    public function setMessageTimestamp($value)
    {
        return $this->setParameter('messageTimestamp', $value);
    }

    public function getMessageID()
    {
        if (!$this->getParameter('messageID'))
        {
            $this->setMessageID($this->generateMessageId());
        }
        return $this->getParameter('messageID');
    }

    public function setMessageID($value)
    {
        return $this->setParameter('messageID', $value);
    }

    protected static function generateMessageId()
    {
        return str_pad(uniqid('1', TRUE), 30, 1);
    }

    /**
     * The format of the Timestamp or Log Time strings returned by NAB Transact XML API is:
     * YYYYDDMMHHnnssKKK000sOOO
     * where:
     *    YYYY = 4-digit year
     *    DD = 2-digit zero-padded day of month
     *    MM = 2-digit zero-padded month of year (January = 01)
     *    HH = 2-digit zero-padded hour of day in 24-hour clock format (midnight =0) is a 2-digit zero-padded minute of hour
     *    NN = 2-digit zero-padded second of minute
     *    SS = 3-digit zero-padded millisecond of second
     *    KKK = Static 0 characters, as NAB Transact does not store nanoseconds
     *    000 = Time zone offset, where s is “+” or “-“, and OOO = minutes, from GMT.
     *    sOOO = 4-digit year.
     *
     * E.g. June 24, 2010 5:12:16.789 PM, Australian EST is:
     *        20102406171216789000+600
     */
    protected static function generateMessageTimestamp()
    {
        list($micro) = explode(' ', microtime());
        $ss = substr($micro, 2, 3);
        $date = date_create();
        date_timezone_set($date, timezone_open('Australia/Melbourne'));
        return date_format($date, "YdmHis{$ss}000") . '+' . ($date->getOffset()/ 60);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

}
