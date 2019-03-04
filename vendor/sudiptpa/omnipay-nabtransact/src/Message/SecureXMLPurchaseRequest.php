<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Purchase Request.
 */
class SecureXMLPurchaseRequest extends SecureXMLAbstractRequest
{
    /**
     * @var int
     */
    protected $txnType = 0;

    /**
     * @var array
     */
    protected $requiredFields = ['amount', 'card', 'transactionId'];

    /**
     * @return string
     */
    public function getData()
    {
        return $this->getBasePaymentXMLWithCard();
    }
}
