<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Authorize Request
 */
class DirectCaptureRequest extends DirectPurchaseRequest
{
    public function getTransactionType()
    {
        return '00002';
    }
}
