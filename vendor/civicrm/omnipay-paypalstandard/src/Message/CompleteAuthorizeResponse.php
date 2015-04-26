<?php

namespace Omnipay\Paypalstandard\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Complete Authorize Response
 */
class CompleteAuthorizeResponse extends AbstractResponse
{
    /**
     * Check the parameters that have been passed in to determine if the response represents a successful transaction
     * This may include decryption or POSTs back to the relevant site or it might just involve interpreting the return
     * parameters
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data['is_success']) && '1' === $this->data['is_success'];
    }

    /**
     * check response for a transaction reference. Most commonly this will be the invoice id
     * @return string|null
     */
    public function getTransactionReference()
    {
        return isset($this->data['reference_id']) ? $this->data['reference_id'] : null;
    }

    /**
     * check response for a message. Usually these are provide in the event of a failure - e.g a decline
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->data['response_message']) ? $this->data['response_message'] : null;
    }
}
