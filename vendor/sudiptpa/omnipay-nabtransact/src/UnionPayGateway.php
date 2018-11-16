<?php

namespace Omnipay\NABTransact;

/**
 * NABTransact UnionPay Gateway.
 */
class UnionPayGateway extends DirectPostGateway
{
    public function getName()
    {
        return 'NABTransact UnionPay';
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\UnionPayPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\UnionPayCompletePurchaseRequest', $parameters);
    }
}
