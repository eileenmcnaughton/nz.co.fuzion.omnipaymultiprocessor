<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * HostedPayment Gateway.
 */
class HostedPaymentGateway extends AbstractGateway
{
    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest', $parameters);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function getName()
    {
        return 'NAB Hosted Payment';
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest', $parameters);
    }

    /**
     * @param $value
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }
}
