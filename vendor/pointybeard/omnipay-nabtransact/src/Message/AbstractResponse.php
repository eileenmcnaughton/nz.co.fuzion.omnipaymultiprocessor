<?php

namespace Omnipay\NABTransact\Message;

abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    public function __construct(PeriodicAbstractRequest $request, $data)
    {
        // The resposnse comes in as a stream. Convert it to a string, and turn into a
        // SimpleXML object.
        return parent::__construct($request, new \SimpleXMLElement((string) $data));
    }
}
