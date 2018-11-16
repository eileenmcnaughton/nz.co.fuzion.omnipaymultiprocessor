<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Capture Request.
 *
 * Capture a partial or full amount that has been held by a prior authorize
 * request.
 *
 * The transactionId (purchaseOrderNo) and preauthID must match the prior
 * authorize transaction for the capture to succeed.
 */
class SecureXMLCaptureRequest extends SecureXMLAbstractRequest
{
    /**
     * @var int
     */
    protected $txnType = 11;

    /**
     * @var array
     */
    protected $requiredFields = ['amount', 'transactionId', 'preauthId'];

    /**
     * @return mixed
     */
    public function getData()
    {
        $xml = $this->getBasePaymentXML();

        $xml->Payment->TxnList->Txn->addChild('preauthID', $this->getPreauthId());

        return $xml;
    }

    /**
     * Set the preauthId that was returned as part of the original authorize request.
     *
     * @return mixed
     */
    public function setPreauthId($value)
    {
        return $this->setParameter('preauthId', $value);
    }

    /**
     * @return string The preauthId from the authorize request that this capture matches.
     */
    public function getPreauthId()
    {
        return $this->getParameter('preauthId');
    }
}
