<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * NABTransact HostedPaymentCompletePurchaseResponse.
 */
class HostedPaymentCompletePurchaseResponse extends AbstractResponse
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
