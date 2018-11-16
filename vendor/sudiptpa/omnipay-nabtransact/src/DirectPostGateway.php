<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * NABTransact Direct Post Gateway.
 */
class DirectPostGateway extends AbstractGateway
{
    /**
     * @var mixed
     */
    public $transparentRedirect = true;

    public function getName()
    {
        return 'NABTransact Direct Post';
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
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }
}
