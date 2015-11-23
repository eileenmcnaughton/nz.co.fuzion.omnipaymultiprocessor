<?php

/**
 * NAB Transact "Customer Management and Payment Scheduling" Gateway.
 */
namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

class TransactGateway extends AbstractGateway
{
    public function getName()
    {
        return 'NAB Transact';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'password' => '',
            'testMode' => false,
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Trigger a payment.
     *
     * Used for initiating a purchase transaction a customer reference number
     * (CRN) in the form of a customerReferenceNumber
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\TransactPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\TransactPurchaseRequest', $parameters);
    }

}
