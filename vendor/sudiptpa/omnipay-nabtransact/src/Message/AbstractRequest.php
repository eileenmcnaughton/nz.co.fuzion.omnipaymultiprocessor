<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Abstract Request.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var mixed
     */
    public $testEndpoint;

    /**
     * @var mixed
     */
    public $liveEndpoint;

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return mixed
     */
    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
