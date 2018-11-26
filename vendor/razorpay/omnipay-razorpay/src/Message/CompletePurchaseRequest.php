<?php

namespace Omnipay\Razorpay\Message;

class CompletePurchaseRequest extends PurchaseRequest
{
    /**
     * Sending data to Response class
     */
    protected function createResponse($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
