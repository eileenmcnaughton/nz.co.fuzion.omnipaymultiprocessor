<?php

namespace Omnipay\FirstAtlanticCommerce\Message;


/**
 * FACPG2 Tokenize Request
 *
 * Required Parameters:
 * customerReference - name of the customer using the card
 * card - Instantiation of Omnipay\FirstAtlanticCommerce\CreditCard()
 *
 */
class CreateCardRequest extends AbstractRequest
{
    /**
     * @var string;
     */
    protected $requestName = 'TokenizeRequest';

    /**
     * Returns the signature for the request.
     *
     * @return string base64 encoded sha1 hash of the merchantPassword, merchantId,
     *    and acquirerId.
     */
    protected function generateSignature()
    {
        $signature  = $this->getMerchantPassword();
        $signature .= $this->getMerchantId();
        $signature .= $this->getAcquirerId();

        return base64_encode( sha1($signature, true) );
    }

    /**
     * Validate and construct the data for the request
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('merchantId', 'merchantPassword', 'acquirerId', 'customerReference', 'card');
        $this->getCard()->validate();

        $data = [
            'CardNumber'        => $this->getCard()->getNumber(),
            'CustomerReference' => $this->getCustomerReference(),
            'ExpiryDate'        => $this->getCard()->getExpiryDate('my'),
            'MerchantNumber'    => $this->getMerchantId(),
            'Signature'         => $this->generateSignature()
        ];

        return $data;
    }

    /**
     * Get the customer reference.
     *
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference.
     *
     * @param string $value
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Returns endpoint for tokenize requests
     *
     * @return string Endpoint URL
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'Tokenize';
    }

    /**
     * Return the tokenize response object
     *
     * @param \SimpleXMLElement $xml Response xml object
     *
     * @return CreateCardResponse
     */
    protected function newResponse($xml)
    {
        return new CreateCardResponse($this, $xml);
    }

}
