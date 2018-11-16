<?php

namespace Omnipay\NABTransact\Message;

use SimpleXMLElement;

/**
 * NABTransact SecureXML Abstract Request.
 */
abstract class SecureXMLAbstractRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint = 'https://demo.transact.nab.com.au/xmlapi/payment';

    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/live/xmlapi/payment';

    /**
     * @var string
     */
    protected $requestType = 'Payment';

    /**
     * @var mixed
     */
    protected $txnType;

    /**
     * @var array
     */
    protected $requiredFields = [];

    /**
     * Set the messageID on the request.
     *
     * This is returned intact on any response so you could add a local
     * database ID here to ease in matching data later.
     */
    public function setMessageId($value)
    {
        return $this->setParameter('messageId', $value);
    }

    /**
     * Generates a SecureXML messageId.
     *
     * @return string
     */
    public function generateMessageId()
    {
        $hash = hash('sha256', microtime());

        return substr($hash, 0, 30);
    }

    /**
     * Get the messageID or generated one based on timestamp.
     *
     * @return string
     */
    public function getMessageId()
    {
        $messageId = $this->getParameter('messageId');

        if (!$this->getParameter('messageId')) {
            $this->setMessageId($this->generateMessageId());
        }

        return $this->getParameter('messageId');
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), [], $data->asXML());

        $xml = new SimpleXMLElement($httpResponse->getBody()->getContents());

        return $this->response = new SecureXMLResponse($this, $xml);
    }

    /**
     * XML Template of a NABTransactMessage.
     *
     * @return SimpleXMLElement NABTransactMessage template.
     */
    protected function getBaseXML()
    {
        foreach ($this->requiredFields as $field) {
            $this->validate($field);
        }

        $xml = new SimpleXMLElement('<NABTransactMessage/>');

        $messageInfo = $xml->addChild('MessageInfo');
        $messageInfo->messageID = $this->getMessageId();
        $messageInfo->addChild('messageTimestamp', $this->generateTimestamp());
        $messageInfo->addChild('timeoutValue', 60);
        $messageInfo->addChild('apiVersion', 'xml-4.2');

        $merchantInfo = $xml->addChild('MerchantInfo');
        $merchantInfo->addChild('merchantID', $this->getMerchantId());
        $merchantInfo->addChild('password', $this->getTransactionPassword());

        $xml->addChild('RequestType', $this->requestType);

        return $xml;
    }

    /**
     * XML template of a NABTransactMessage Payment.
     *
     * @return SimpleXMLElement NABTransactMessage with transaction details.
     */
    protected function getBasePaymentXML()
    {
        $xml = $this->getBaseXML();

        $payment = $xml->addChild('Payment');
        $txnList = $payment->addChild('TxnList');
        $txnList->addAttribute('count', 1);
        $transaction = $txnList->addChild('Txn');
        $transaction->addAttribute('ID', 1);
        $transaction->addChild('txnType', $this->txnType);
        $transaction->addChild('txnSource', 23);
        $transaction->addChild('txnChannel', 0);
        $transaction->addChild('amount', $this->getAmountInteger());
        $transaction->addChild('currency', $this->getCurrency());
        $transaction->addChild('purchaseOrderNo', $this->getTransactionId());

        return $xml;
    }

    /**
     * NABTransactMessage with transaction and card details.
     *
     * @return SimpleXMLElement
     */
    protected function getBasePaymentXMLWithCard()
    {
        $this->getCard()->validate();

        $xml = $this->getBasePaymentXML();

        $card = $xml->Payment->TxnList->Txn->addChild('CreditCardInfo');
        $card->addChild('cardNumber', $this->getCard()->getNumber());
        $card->addChild('cvv', $this->getCard()->getCvv());
        $card->addChild('expiryDate', $this->getCard()->getExpiryDate('m/y'));
        $card->addChild('cardHolderName', $this->getCard()->getName());
        $card->addChild('recurringflag', 'no');

        return $xml;
    }

    /**
     * Generates a SecureXML timestamp.
     *
     * SecureXML requires a specific timestamp format as per appendix F of the
     * documentation.
     *
     * @return string SecureXML formatted timestamp.
     */
    protected function generateTimestamp()
    {
        $date = new \DateTime();

        return $date->format(sprintf('YmdHis000%+04d', $date->format('Z') / 60));
    }
}
