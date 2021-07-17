<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\FirstAtlanticCommerce\Message\AbstractResponse;

/**
 * FACPG2 XML Authorize Response
 */
class AuthorizeResponse extends AbstractResponse
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

        $this->verifySignature();
    }

    /**
     * Verifies the signature for the response.
     *
     * @throws InvalidResponseException if the signature doesn't match
     *
     * @return void
     */
    public function verifySignature()
    {
        if ( isset($this->data['CreditCardTransactionResults']['ResponseCode']) && (
            '1' == $this->data['CreditCardTransactionResults']['ResponseCode'] ||
            '2' == $this->data['CreditCardTransactionResults']['ResponseCode']) )
        {
            $signature  = $this->request->getMerchantPassword();
            $signature .= $this->request->getMerchantId();
            $signature .= $this->request->getAcquirerId();
            $signature .= $this->request->getTransactionId();

            $signature  = base64_encode( sha1($signature, true) );

            if ( $signature !== $this->data['Signature'] ) {
                throw new InvalidResponseException('Signature verification failed');
            }
        }
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
     * Return the response's reason code
     *
     * @return string
     */
    public function getCode()
    {
        return isset($this->data['CreditCardTransactionResults']['ReasonCode']) ? $this->data['CreditCardTransactionResults']['ReasonCode'] : null;
    }

    /**
     * Return the response's reason message
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

    /**
     * If the createCard parameter is set to true on the authorize request this will return the token
     *
     * @return string|null
     */
    public function getCardReference()
    {
        return isset($this->data['CreditCardTransactionResults']['TokenizedPAN']) ? $this->data['CreditCardTransactionResults']['TokenizedPAN'] : null;
    }
}
