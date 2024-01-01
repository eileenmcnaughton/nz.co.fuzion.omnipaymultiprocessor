<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Abstract Request.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint;

    /**
     * @var string
     */
    public $liveEndpoint;

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    /**
     * @param $value
     */
    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    public function getHasEMV3DSEnabled()
    {
        return $this->getParameter('hasEMV3DSEnabled');
    }

    /**
     * @param $value
     */
    public function setHasEMV3DSEnabled($value)
    {
        return $this->setParameter('hasEMV3DSEnabled', $value);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
