<?php

namespace Omnipay\Beanstream\Message;

/**
 * Class CaptureRequest
 *
 * The data for this is the same as an authorization except the endpoint references the original transaction id and
 * complete is set to true on the card.
 *
 * @package Omnipay\Beanstream\Message
 */
class CaptureRequest extends AuthorizeRequest
{
    /** @var bool Overwrites that same value in the AuthorizeRequest */
    protected $complete = true;

    /**
     * Create the endpoint for a capture AKA a completion in Beanstream
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint . '/payments/' . $this->getTransactionReference() . '/completions';
    }
}
