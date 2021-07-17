<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

/**
 * FACPG2 Transaction Modification Response
 */
class TransactionModificationResponse extends AbstractResponse
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param string $data
     *
     * @throws InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        if ( empty($data) )
        {
            throw new InvalidResponseException();
        }

        $this->request = $request;
        $this->data    = $this->xmlDeserialize($data);
    }

    /**
     * Return whether or not the response was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return isset($this->data['ResponseCode']) && '1' === $this->data['ResponseCode'];
    }

    /**
     * Return the response's reason code
     *
     * @return string
     */
    public function getCode()
    {
        return isset($this->data['ReasonCode']) ? $this->data['ReasonCode'] : null;
    }

    /**
     * Return the response's reason message
     *
     * @return string
     */
    public function getMessage()
    {
        return isset($this->data['ReasonCodeDescription']) ? $this->data['ReasonCodeDescription'] : null;
    }
}
