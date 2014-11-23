<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Create Credit Card Request
 */
class SystemCreateCardRequest extends SystemAuthorizeRequest
{
    public function getTransactionType()
    {
        return '00056';
    }
}
