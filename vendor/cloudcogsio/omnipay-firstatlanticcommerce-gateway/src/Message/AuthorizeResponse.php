<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Exception\InvalidResponseData;
use Omnipay\FirstAtlanticCommerce\Support\CreditCardTransactionResults;

class AuthorizeResponse extends AbstractResponse
{
    protected $CreditCardTransactionResults;

    public function isSuccessful()
    {
        if (intval($this->getCreditCardTransactionResults()->getResponseCode()) === 1) return true;

        return false;
    }

    public function getMessage()
    {
        return $this->getCreditCardTransactionResults()->getReasonCodeDescription();
    }

    public function getCode()
    {
        return $this->getCreditCardTransactionResults()->getReasonCode();
    }

    public function getTransactionReference()
    {
        return $this->getCreditCardTransactionResults()->getReferenceNumber();
    }

    public function getTransactionId()
    {
        return $this->getRequest()->getTransactionId();
    }

    public function verifySignature()
    {
        $FACSignature= $this->getSignature();
        $ValidatedSignature = base64_encode(sha1($this->getRequest()->getFacPwd().$this->getRequest()->getFacId().$this->getRequest()->getFacAcquirer().$this->getRequest()->getTransactionId(),true));
        if ($FACSignature !== $ValidatedSignature)
            throw new InvalidResponseData("Signature mismatch");

        return $this;
    }

    public function getAcquirerId()
    {
        return $this->getRequest()->getFacAcquirer();
    }

    public function getCustomData() : \SimpleXMLElement
    {
        return $this->getData()->xpath("//fac:CustomData");
    }

    public function getIPAddress()
    {
        return $this->getRequest()->getClientIp();
    }

    public function getMerchantID()
    {
        return $this->getRequest()->getFacId();
    }

    public function getReferenceNumber()
    {
        return $this->queryData("ReferenceNumber");
    }

    public function getSignature()
    {
        return $this->queryData("Signature");
    }

    public function getSignatureMethod()
    {
        return $this->queryData("SignatureMethod");
    }

    public function getCreditCardTransactionResults() : CreditCardTransactionResults
    {
        if (!$this->CreditCardTransactionResults)
        {
            $this->CreditCardTransactionResults = new CreditCardTransactionResults($this->getRequest(), $this->getData());
        }

        return $this->CreditCardTransactionResults;
    }
}