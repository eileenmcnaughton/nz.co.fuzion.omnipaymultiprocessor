<?php

namespace Omnipay\Paybox\Message;

/**
 * Cybersource Authorize Request
 */
class CaptureRequest extends PurchaseRequest
{
    public function getTransactionType()
    {
        return '00002';
    }
}
