<?php

namespace Omnipay\Buckaroo;

/**
 * Buckaroo PayPal Gateway
 */
class PayPalGateway extends Gateway
{
    public function getName()
    {
        return 'Buckaroo PayPal';
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Buckaroo\Message\PayPalPurchaseRequest', $parameters);
    }
}
