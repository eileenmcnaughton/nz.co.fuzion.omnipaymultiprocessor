<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Authorize Request.
 *
 * Verify that the amount is available and hold for capture.
 *
 * Returns a 'preauthID' value that must be supplied in any subsequent capture
 * request.
 */
class SecureXMLAuthorizeRequest extends SecureXMLAbstractRequest
{
    /**
     * @var int
     */
    protected $txnType = 10;

    /**
     * @var array
     */
    protected $requiredFields = ['amount', 'card', 'transactionId'];

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getBasePaymentXMLWithCard();
    }
}
