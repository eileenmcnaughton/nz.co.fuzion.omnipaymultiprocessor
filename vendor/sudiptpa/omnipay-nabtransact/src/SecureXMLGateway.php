<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * NABTransact Secure XML Gateway.
 */
class SecureXMLGateway extends AbstractGateway
{
    public function getName()
    {
        return 'NAB Transact XML';
    }

    public function getDefaultParameters()
    {
        return [
            'merchantId'          => '',
            'transactionPassword' => '',
            'testMode'            => false,
        ];
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    /**
     * @param $value
     */
    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLCaptureRequest
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLRefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLRefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLEchoTestRequest
     */
    public function echoTest(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLEchoTestRequest', $parameters);
    }
}
