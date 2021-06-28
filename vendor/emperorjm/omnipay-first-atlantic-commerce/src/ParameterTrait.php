<?php

namespace Omnipay\FirstAtlanticCommerce;

trait ParameterTrait
{
    /**
     * @param string $value Merchant ID.
     *
     * @return string $value Merchant ID.
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string $value Merchant ID.
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param string $value Merchant Password.
     *
     * @return string $value Merchant Password.
     */
    public function setMerchantPassword($value)
    {
        return $this->setParameter('merchantPassword', $value);
    }

    /**
     * @return string $value Merchant Password.
     */
    public function getMerchantPassword()
    {
        return $this->getParameter('merchantPassword');
    }

    /**
     * @param string $value Acquirer ID.
     *
     * @return string $value Acquirer ID.
     */
    public function setAcquirerId($value)
    {
        return $this->setParameter('acquirerId', $value);
    }

    /**
     * @return string $value Acquirer ID.
     */
    public function getAcquirerId()
    {
        return $this->getParameter('acquirerId');
    }

    /**
     * @param boolean $value Require AVS Check.
     *
     * @return boolean $value Require AVS Check.
     */
    public function setRequireAvsCheck($value)
    {
        return $this->setParameter('requireAvsCheck', $value);
    }

    /**
     * @return boolean $value Require AVS Check.
     */
    public function getRequireAvsCheck()
    {
        return $this->getParameter('requireAvsCheck');
    }
}
