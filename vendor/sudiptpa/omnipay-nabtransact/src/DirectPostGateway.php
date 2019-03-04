<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * NABTransact Direct Post Gateway.
 *
 * @link https://demo.transact.nab.com.au/nabtransact/downloadDocs.nab?nav=3-4
 */
class DirectPostGateway extends AbstractGateway
{
    /**
     * @var bool
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
     * @return \Omnipay\NABTransact\Message\DirectPostAuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }
}
