<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Purchase Request
 */
class DirectPurchaseRequest extends DirectAuthorizeRequest
{
    public function getTransactionType()
    {
        return '00003';
    }
}
