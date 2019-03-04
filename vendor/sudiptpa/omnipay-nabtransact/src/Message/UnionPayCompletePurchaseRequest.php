<?php

namespace Omnipay\NABTransact\Message;

/**
 * UnionPayCompletePurchaseRequest.
 */
class UnionPayCompletePurchaseRequest extends DirectPostCompletePurchaseRequest
{
    /**
     * @param $data
     *
     * @return \Omnipay\NABTransact\Message\UnionPayCompletePurchaseResponse
     */
    public function sendData($data)
    {
        $this->response = new UnionPayCompletePurchaseResponse($this, $data);

        return $this->response;
    }
}
