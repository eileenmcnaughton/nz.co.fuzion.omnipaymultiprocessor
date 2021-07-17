<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\FirstAtlanticCommerce\Message\AbstractResponse;

/**
 * FACPG2 Transaction Status Response
 */
class StatusResponse extends AbstractResponse
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param string $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        if ( empty($data) ) {
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
        return isset($this->data['CreditCardTransactionResults']['ResponseCode']) && '1' === $this->data['CreditCardTransactionResults']['ResponseCode'];
    }

    /**
     * Return the response message
     *
     * @return string
     */
    public function getMessage()
    {
        return isset($this->data['CreditCardTransactionResults']['ReasonCodeDescription']) ? $this->data['CreditCardTransactionResults']['ReasonCodeDescription'] : null;
    }

    /**
     * Return transaction reference
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return isset($this->data['CreditCardTransactionResults']['ReferenceNumber']) ? $this->data['CreditCardTransactionResults']['ReferenceNumber'] : null;
    }
}
