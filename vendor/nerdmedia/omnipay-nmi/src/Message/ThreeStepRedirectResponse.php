<?php

namespace Omnipay\NMI\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * NMI Three Step Redirect Response
 */
class ThreeStepRedirectResponse extends AbstractResponse
{
    /**
     * @param \Omnipay\Common\Message\RequestInterface
     * @param \SimpleXMLElement
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return '1' === $this->getCode();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return trim($this->data->{'result'});
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return trim($this->data->{'result-code'});
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return trim($this->data->{'result-text'});
    }

    public function getAuthorizationCode()
    {
        return trim($this->data->{'authorization-code'});
    }

    /**
     * @return string
     */
    public function getAVSResponse()
    {
        return trim($this->data->{'avs-result'});
    }

    /**
     * @return string
     */
    public function getCVVResponse()
    {
        return trim($this->data->{'cvv-result'});
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return trim($this->data->{'order-id'});
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        return trim($this->data->{'transaction-id'});
    }

    /**
     * @return string|null
     */
    public function getCardReference()
    {
        if (isset($this->data->{'customer-vault-id'})) {
            return trim($this->data->{'customer-vault-id'});
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getFormUrl()
    {
        if (isset($this->data->{'form-url'})) {
            return trim($this->data->{'form-url'});
        }

        return null;
    }
}
