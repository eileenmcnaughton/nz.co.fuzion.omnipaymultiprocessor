<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Refund Request.
 *
 * Refund a partial or full amount from a prior purchase.
 *
 * The transactionReference (txnID) and transactionId (purchaseOrderNo) must
 * match the original transaction for a refund to be successful.
 */
class SecureXMLRefundRequest extends SecureXMLAbstractRequest
{
    /**
     * @var int
     */
    protected $txnType = 4;

    /**
     * @var array
     */
    protected $requiredFields = ['amount', 'transactionId', 'transactionReference'];

    /**
     * @return mixed
     */
    public function getData()
    {
        $xml = $this->getBasePaymentXML();
        $xml->Payment->TxnList->Txn->addChild('txnID', $this->getTransactionId());
        $xml->Payment->TxnList->Txn->addChild('purchaseOrderNo', $this->getTransactionReference());

        return $xml;
    }
}
