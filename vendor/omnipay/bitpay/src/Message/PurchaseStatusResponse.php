<?php

namespace Omnipay\BitPay\Message;

/**
 * BitPay Purchase Status Response
 */
class PurchaseStatusResponse extends PurchaseResponse
{
    public function isSuccessful()
    {
        return !$this->getMessage();
    }

    public function isRedirect()
    {
        return false;
    }
}
