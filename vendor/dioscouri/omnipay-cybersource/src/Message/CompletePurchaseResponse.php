<?php

namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * New Complete Purchase response
 */
class CompletePurchaseResponse extends CompleteAuthorizeResponse
{
    public function isSuccessful()
    {
        return isset($this->data['decision']) && 'ACCEPT' === $this->data['decision'];
    }

    public function getTransactionId()
    {
        return isset($this->data['req_reference_number']) ? $this->data['req_reference_number'] : null;
    }

    public function getTransactionReference()
    {
        return isset($this->data['transaction_id']) ? $this->data['transaction_id'] : null;
    }

    public function getMessage()
    {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }
}
