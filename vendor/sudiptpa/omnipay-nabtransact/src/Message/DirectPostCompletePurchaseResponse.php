<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * NABTransact Direct Post Complete Purchase Response.
 */
class DirectPostCompletePurchaseResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->summaryCode() && in_array($this->getCode(), ['00', '08', '11']);
    }

    public function summaryCode()
    {
        return isset($this->data['summarycode']) && (int) $this->data['summarycode'] == 1;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if (isset($this->data['restext'])) {
            return $this->data['restext'];
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if (isset($this->data['rescode'])) {
            return $this->data['rescode'];
        }
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        if (isset($this->data['txnid'])) {
            return $this->data['txnid'];
        }
    }
}
