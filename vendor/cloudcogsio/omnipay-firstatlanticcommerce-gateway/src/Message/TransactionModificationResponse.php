<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

class TransactionModificationResponse extends AbstractResponse
{
    /**
     * {@inheritDoc}
     * @see \Omnipay\FirstAtlanticCommerce\Message\AbstractResponse::verifySignature()
     */
    public function verifySignature()
    {
        return $this;
    }

    public function isSuccessful()
    {
        if (intval($this->queryData("ResponseCode")) === 1) return true;

        return false;
    }

    public function getMessage()
    {
        return $this->queryData("ReasonCodeDescription");
    }

    public function getCode()
    {
        return $this->queryData("ReasonCode");
    }

    public function getTransactionId()
    {
        return $this->getRequest()->getTransactionId();
    }

    public function getAcquirerId()
    {
        return $this->getRequest()->getFacAcquirer();
    }

    public function getMerchantID()
    {
        return $this->getRequest()->getFacId();
    }

    public function getOrderNumber()
    {
        return $this->getRequest()->getTransactionId();
    }

    public function getOriginalResponseCode()
    {
        return $this->queryData("OriginalResponseCode");
    }

    public function getReasonCode()
    {
        return $this->queryData("ReasonCode");
    }

    public function getReasonCodeDescription()
    {
        return $this->queryData("ReasonCodeDescription");
    }

    public function getResponseCode()
    {
        return $this->queryData("ResponseCode");
    }
}