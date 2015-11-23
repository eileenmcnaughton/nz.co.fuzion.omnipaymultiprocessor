<?php

namespace Omnipay\NABTransact\Message;

class PeriodicResponse extends AbstractResponse
{
    public static $STATUS_CODES = [
          0 => 'Normal',
        300 => 'Invalid Amount',
        301 => 'Invalid Credit Card Number',
        302 => 'Invalid Expiry Date',
        303 => 'Invalid CRN',
        304 => 'Invalid Merchant ID',
        305 => 'Invalid BSB Number',
        306 => 'Invalid Account Number',
        307 => 'Invalid Account Name',
        309 => 'Invalid CVV Number',
        313 => 'General Database Error',
        314 => 'Unable to Read Properties File',
        316 => 'Invalid Action Type',
        318 => 'Unable to Decrypt Account Details',
        327 => 'Invalid Periodic Payment Type',
        328 => 'Invalid Periodic Frequency',
        329 => 'Invalid Number of Payments',
        332 => 'Invalid Date Format',
        333 => 'Triggered Payment Not Found',
        346 => 'Duplicate CRN Found',
        347 => 'Duplicate Allocated Variable Found',
        504 => 'Invalid Merchant ID',
        505 => 'Invalid URL',
        510 => 'Unable To Connect To Server',
        511 => 'Server Connection Aborted During Transaction',
        512 => 'Transaction timed out By Client',
        513 => 'General Database Error',
        514 => 'Error loading properties file',
        515 => 'Fatal Unknown Error',
        516 => 'Request type unavailable',
        517 => 'Message Format Error',
        518 => 'Customer Not Registered',
        524 => 'Response not received',
        545 => 'System maintenance in progress',
        550 => 'Invalid password',
        575 => 'Not implemented',
        577 => 'Too Many Records for Processing',
        580 => 'Process method has not been called',
        595 => 'Merchant Disabled',
    ];

    public function isRedirect()
    {
        return false;
    }

    public function isSuccessful()
    {
        return ($this->getStatusCode() == 0 && $this->getCode() == '00');
    }

    public function getMessageId()
    {
        return $this->getMessageInfo()['messageID'];
    }

    public function getMessageInfo()
    {
        return (array) $this->data->MessageInfo;
    }

    public function getStatusCode()
    {
        return (int) $this->data->Status->statusCode;
    }

    public function getMessage()
    {
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->responseText;
    }

    public function getCode()
    {
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->responseCode;
    }

    public function getCustomerReference()
    {
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->crn;
    }

    public function getTransactionId()
    {
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->txnID;
    }

    public function getTransactionReference()
    {
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->transactionReference;
    }

    public function getTransactionCurrency(){
        return (string) $this->data->Periodic->PeriodicList->PeriodicItem->currency;
    }

}
