<?php

namespace Omnipay\AuthorizeNetStd\Message;

/**
 * Authorize.Net Capture Request
 */
class AIMCaptureRequestStd extends AIMAbstractRequestStd
{
    protected $action = 'priorAuthCaptureTransaction';

    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $data->transactionRequest->refTransId = $this->getTransactionReference();
        $this->addTransactionSettings($data);

        return $data;
    }
}
