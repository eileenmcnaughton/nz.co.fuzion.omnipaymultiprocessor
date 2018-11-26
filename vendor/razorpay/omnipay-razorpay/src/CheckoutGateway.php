<?php

namespace Omnipay\Razorpay;

use Omnipay\Common\AbstractGateway;

/**
 * Razorpay Payment Gateway
 */
class CheckoutGateway extends AbstractGateway
{
    // Gateway name
    public function getName()
    {
        return 'Razorpay';
    }

    public function getDefaultParameters()
    {
        return array(
            'key_id' => '',
            'key_secret' => ''
        );
    }

    public function getKeyID()
    {
        return $this->getParameter('key_id');
    }

    public function setKeyID($value)
    {
        return $this->setParameter('key_id', $value);
    }

    public function getKeySecret()
    {
        return $this->getParameter('key_secret');
    }

    public function setKeySecret($value)
    {
        return $this->setParameter('key_secret', $value);
    }

    /**
     * Creating the Purchase Request
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Razorpay\Message\PurchaseRequest', $parameters);
    }

    /**
     * Verifying the Purchase Request
     */
    public function completePurchase(array $parameters = array())
    {
        $parameters['key_secret'] = $this->getKeySecret();
        return $this->createRequest('\Omnipay\Razorpay\Message\CompletePurchaseRequest', $parameters);
    }
}
