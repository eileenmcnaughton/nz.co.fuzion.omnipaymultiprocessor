<?php
namespace Omnipay\Pergo\Message;

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
