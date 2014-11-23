<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox System Authorize Request
 */
class SystemCaptureRequest extends SystemPurchaseRequest
{
    public function getTransactionType()
    {
        return '00002';
    }
}
