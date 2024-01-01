<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * SystemPay Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{

    use GetMetadataTrait;

    public function isSuccessful()
    {
        return in_array($this->getTransactionStatus(), ['AUTHORISED', 'CAPTURED']);
    }

    public function getTransactionReference()
    {
        return isset($this->data['vads_trans_id']) ? $this->data['vads_trans_id'] : null;
    }

    public function getOrderId()
    {
        return isset($this->data['vads_order_id']) ? $this->data['vads_order_id'] : null;
    }

    public function getAmount()
    {
        return isset($this->data['vads_amount']) ? $this->data['vads_amount'] / 100 : null;
    }

    public function getTransactionDate()
    {
        return isset($this->data['vads_trans_date']) ? $this->data['vads_trans_date'] : null;
    }

    public function getTransactionStatus()
    {
        return isset($this->data['vads_trans_status']) ? $this->data['vads_trans_status'] : null;
    }

    public function getCode()
    {
        return isset($this->data['vads_result']) ? $this->data['vads_result'] : null;
    }

    public function getTransactionId()
    {
        return isset($this->data['vads_trans_id']) ? $this->data['vads_trans_id'] : null;
    }

    public function getUuid()
    {
        return isset($this->data['vads_trans_uuid']) ? $this->data['vads_trans_uuid'] : null;
    }
}
