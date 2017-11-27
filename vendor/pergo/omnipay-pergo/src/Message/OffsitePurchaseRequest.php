<?php
namespace Omnipay\pergo\Message;

/**
 * Purchase Request
 */
class OffsitePurchaseRequest extends OffsiteAuthorizeRequest
{
    public function getTransactionType()
    {
        return 'sale';
    }
}
