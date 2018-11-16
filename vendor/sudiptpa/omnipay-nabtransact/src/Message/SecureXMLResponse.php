<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;

class SecureXMLResponse extends AbstractResponse
{
    /**
     * Determine if the transaction is successful or not.
     *
     * @note Rather than using HTTP status codes, the SecureXML API returns a
     * status code as part of the response if there is an internal API issue.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getStatusCode() == '000' && $this->getApproved();
    }

    /**
     * Determine if we have had payment information returned.
     *
     * @note For certain errors a Payment element is returned but has an empty
     * TxnList so this will tell us if we actually have a transaction to check.
     *
     * @return bool True if we have a transaction.
     */
    protected function hasTransaction()
    {
        return isset($this->data->Payment->TxnList->Txn);
    }

    /**
     * Gateway status code if available.
     *
     * @return string
     */
    public function getStatusCode()
    {
        return (string) $this->data->Status->statusCode;
    }

    /**
     * Gateway RequestType if available.
     *
     * @return string
     */
    public function getRequestType()
    {
        return (string) $this->data->RequestType;
    }

    /**
     * Gateway approved string if available.
     *
     * @return string
     */
    public function getApproved()
    {
        return $this->hasTransaction() && (string) $this->data->Payment->TxnList->Txn->approved == 'Yes';
    }

    /**
     * Gateway responseText if available.
     *
     * @return string
     */
    public function getResponseText()
    {
        return (string) $this->data->Payment->TxnList->Txn->responseText;
    }

    /**
     * Gateway failure code or transaction code if available.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->responseCode
            : (string) $this->data->Status->statusCode;
    }

    /**
     * Gateway failure message or transaction message if available.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->responseText
            : (string) $this->data->Status->statusDescription;
    }

    /**
     * Gateway message timestamp if available.
     *
     * @return string
     */
    public function getMessageTimestamp()
    {
        return (string) $this->data->MessageInfo->messageTimestamp;
    }

    /**
     * @return string Unique NABTransact bank transaction reference.
     */
    public function getTransactionReference()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->purchaseOrderNo
            : null;
    }

    /**
     * @return string Unique NABTransact bank transaction reference.
     */
    public function getTransactionId()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->txnID
            : null;
    }

    /**
     * NABTransact transaction amount.
     *
     * @return string
     */
    public function getTransactionAmount()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->amount
            : null;
    }

    /**
     * NABTransact transaction currency.
     *
     * @return string
     */
    public function getTransactionCurrency()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->currency
            : null;
    }

    /**
     * NABTransact transaction source.
     *
     * @return mixed
     */
    public function getTransactionSource()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->txnSource
            : null;
    }

    /**
     * Settlement date when the funds will be settled into the merchants account.
     *
     * @return string|null
     */
    public function getSettlementDate()
    {
        return $this->hasTransaction()
            ? (string) $this->data->Payment->TxnList->Txn->settlementDate
            : null;
    }
}
