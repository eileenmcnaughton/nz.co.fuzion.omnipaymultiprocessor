<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Message\AbstractRequest;

/**
 * FACPG2 Update Token Request
 *
 * Required Parameters:
 * customerReference - The name of the customer
 * cardReference - This is the token created by FAC for the card being updated
 * card - Instantiation of the Omnipay\FirstAtlanticCommerce\CreditCard class
 */
class UpdateCardRequest extends AbstractRequest
{
    /**
     * @var string;
     */
    protected $requestName = 'UpdateTokenRequest';

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
        $this->validate('merchantId', 'merchantPassword', 'acquirerId', 'customerReference', 'cardReference', 'card');
        $this->getCard()->validate();

        $data = [
            'CustomerReference' => $this->getCustomerReference(),
            'ExpiryDate'        => $this->getCard()->getExpiryDate('my'),
            'MerchantNumber'    => $this->getMerchantId(),
            'Signature'         => $this->generateSignature(),
            'TokenPAN'          => $this->getCardReference()
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
     * Returns endpoint for update token requests
     *
     * @return string Endpoint URL
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'UpdateToken';
    }

    /**
     * Return the update token response object
     *
     * @param \SimpleXMLElement $xml Response xml object
     *
     * @return UpdateCardResponse
     */
    protected function newResponse($xml)
    {
        return new UpdateCardResponse($this, $xml);
    }

}
