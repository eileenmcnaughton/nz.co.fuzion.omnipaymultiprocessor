<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Purchase Request.
 */
class DirectPostPurchaseRequest extends DirectPostAuthorizeRequest
{
    /**
     * @var string
     */
    public $txnType = '0';
}
