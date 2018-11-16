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
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return mixed
     */
    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLRefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function echoTest(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLEchoTestRequest', $parameters);
    }
}
