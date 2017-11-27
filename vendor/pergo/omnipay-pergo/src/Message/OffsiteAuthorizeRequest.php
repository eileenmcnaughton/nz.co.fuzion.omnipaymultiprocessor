<?php
namespace Omnipay\pergo\Message;

use Omnipay\pergo\Message\AbstractRequest;

/**
 * pergo Authorize Request
 */
class OffsiteAuthorizeRequest extends OffsiteAbstractRequest
{

    /**
     * sendData function. In this case, where the browser is to be directly it constructs and returns a response object
     * @param mixed $data
     * @return \Omnipay\Common\Message\ResponseInterface|OffsiteAuthorizeResponse
     */
    public function sendData($data)
    {
        return $this->response = new OffsiteAuthorizeResponse($this, $data, $this->getEndpoint());
    }

    /**
     * Get an array of the required fields for the core gateway
     * @return array
     */
    public function getRequiredCoreFields()
    {
        return array
        (
            'amount',
            'currency',
        );
    }

    /**
     * get an array of the required 'card' fields (personal information fields)
     * @return array
     */
    public function getRequiredCardFields()
    {
        return array
        (
            'email',
        );
    }

    /**
     * Map Omnipay normalised fields to gateway defined fields. If the order the fields are
     * passed to the gateway matters you should order them correctly here
     *
     * @fixMe you will need to update this to reflect the processor.
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getTransactionData()
    {
        return array
        (
            'site_ref' => $this->getTransactionId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrencyNumeric(),
        );
    }

    /**
     * @return array
     * Get data that is common to all requests - generally aut
     */
    public function getBaseData()
    {
        return array(
            'type' => $this->getTransactionType(),
            'authenticationtoken' => $this->getAuthenticationToken(),
            'billeraccountid' => $this->getBillerAccountId(),
        );
    }

    /**
     * this is the url provided by your payment processor. Github is standing in for the real url here
    * @return string
    */
    public function getEndpoint()
    {
        return 'https://protectpaytest.propay.com/hpp/v2/[hostedtransactionidentifier';
    }

    public function getTransactionType()
    {
        return 'Authorize';
    }
}
