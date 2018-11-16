<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Echo Request.
 *
 * Echo requests are used to verify that the NABTransact payment server is
 * available.
 *
 * The status code returned in the response will be '000' if the service is up.
 */
class SecureXMLEchoTestRequest extends SecureXMLAbstractRequest
{
    /**
     * @var string
     */
    protected $requestType = 'Echo';

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getBaseXML();
    }
}
