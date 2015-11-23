<?php

/**
 * NAB Transact "Customer Management and Payment Scheduling" Gateway.
 */
namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

class PeriodicGateway extends AbstractGateway
{
    public function getName()
    {
        return 'NAB Transact - Customer Management and Payment Scheduling';
    }

    public function getDefaultParameters()
    {
        return [
            'merchantId' => '',
            'password' => '',
            'testMode' => false,
            'apiVersion' => 'spxml-4.2',
        ];
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

    public function getApiVersion()
    {
        return $this->getParameter('apiVersion');
    }

    public function setApiVersion($value)
    {
        return $this->setParameter('apiVersion', $value);
    }

    /**
     * Trigger a payment.
     *
     * Used for initiating a purchase transaction a customer reference number
     * (CRN) in the form of a customerReferenceNumber
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\PeriodicTriggerPaymentRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\PeriodicTriggerPaymentRequest', $parameters);
    }

    /**
     * Store a credit card as a Customer Reference Number (CRN).
     *
     * You can currently securely store card details with NAB for future
     * charging using a CRN.
     * After storing the card, pass the customerReferenceNumber instead of the card
     * details to complete a payment.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\PeriodicCreateCustomerRequest
     */
    public function createCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\PeriodicCreateCustomerRequest', $parameters);
    }

    /**
     * Update a credit card stored as a Customer Reference Number (CRN).
     *
     * You can currently securely store card details with NAB for future
     * charging using a CRN.
     * This resource requires the customerReferenceNumber for the card to be updated.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\PeriodicUpdateCustomerRequest
     */
    public function updateCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\PeriodicUpdateCustomerRequest', $parameters);
    }

    /**
     * Delete a credit card stored as a Customer Reference Number (CRN).
     *
     * You can currently securely store card details with NAB for future
     * charging using a CRN.
     * This resource requires the customerReferenceNumber for the card to be updated.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\PeriodicDeleteCustomerRequest
     */
    public function deleteCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\PeriodicDeleteCustomerRequest', $parameters);
    }
}
