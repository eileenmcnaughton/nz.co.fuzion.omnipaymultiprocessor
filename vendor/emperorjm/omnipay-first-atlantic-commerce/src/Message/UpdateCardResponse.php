<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\FirstAtlanticCommerce\Message\AbstractResponse;

/**
 * FACPG2 XML Update Token Response
 */
class UpdateCardResponse extends AbstractResponse
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param string $data
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
        return isset($this->data['Success']) && 'true' === $this->data['Success'];
    }

    /**
     * Return the response's reason message
     *
     * @return string
     */
    public function getMessage()
    {
        return isset($this->data['ErrorMsg']) && !empty($this->data['ErrorMsg']) ? $this->data['ErrorMsg'] : null;
    }

    /**
     * Return card reference
     *
     * @return string
     */
    public function getCardReference()
    {
        return isset($this->data['TokenPAN']) ? $this->data['TokenPAN'] : null;
    }

}
