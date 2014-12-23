<?php

namespace Omnipay\Cybersource\Message;

/**
 * Cybersource Purchase Request
 */
class PurchaseRequest extends AuthorizeRequest
{
    public function getTransactionType()
    {
        return 'sale';
    }
}
