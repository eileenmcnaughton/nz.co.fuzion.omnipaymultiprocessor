<?php

/**
 *
 */
namespace Omnipay\NABTransact\Message;
use SimpleXMLElement;

/**
 *
 */
final class TransactPurchaseRequest extends TransactAbstractRequest
{

    public function getActionType()
    {
        return '';
    }


    protected function getRequestType() {
        return 'Payment';
    }

}
