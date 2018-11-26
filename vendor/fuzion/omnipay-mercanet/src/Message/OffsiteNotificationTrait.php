<?php

namespace Omnipay\Mercanet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;

trait OffsiteNotificationTrait
{

    /**
     * Check the parameters that have been passed in to determine if the response represents a successful transaction.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data['responseCode']) && '00' === $this->data['responseCode'];
    }

    /**
     * Check response for a transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        return isset($this->data['authorisationId']) ? $this->data['authorisationId'] : null;
    }

    /**
     * check response for a message. Usually these are provide in the event of a failure - e.g a decline
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->data['response_message']) ? $this->data['response_message'] : null;
    }

    /**
     * Check response for a transaction ID.
     *
     * Most commonly this will be the invoice id.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return isset($this->data['transactionReference']) ? $this->data['transactionReference'] : null;
    }
}
