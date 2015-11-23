<?php

namespace Omnipay\NABTransact\Message;

abstract class TransactAbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    public function __construct(TransactAbstractRequest $request, $data)
    {
        return parent::__construct($request, new \SimpleXMLElement((string) $data));
    }
}
