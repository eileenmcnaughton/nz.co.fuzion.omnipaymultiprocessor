<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Purchase Request
 */
class SystemPurchaseRequest extends SystemAuthorizeRequest
{
    public function getTransactionType()
    {
        return '00003';
    }
}
