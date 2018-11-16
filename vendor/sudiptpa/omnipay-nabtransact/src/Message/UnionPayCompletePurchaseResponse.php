<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * UnionPayCompletePurchaseResponse.
 */
class UnionPayCompletePurchaseResponse extends DirectPostCompletePurchaseResponse
{
    /**
     * @param RequestInterface $request
     * @param $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        if (!is_array($data)) {
            parse_str($data, $data);
        }

        parent::__construct($request, $data);
    }
}
