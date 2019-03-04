<?php

namespace Omnipay\NABTransact;

/**
 * NABTransact UnionPay Gateway.
 */
class UnionPayGateway extends DirectPostGateway
{
    public function getName()
    {
        return 'NAB Transact UnionPay';
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\UnionPayPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\UnionPayPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\UnionPayCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\UnionPayCompletePurchaseRequest', $parameters);
    }
}
