<?php
namespace Omnipay\Mercanet\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Request
 */
abstract class OffsiteAbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $data;

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    protected $seal;

    /**
     * @param mixed $seal
     */
    public function setSeal($seal)
    {
        $this->seal = $seal;
    }

    protected $interfaceVersion;
    /**
     * @param mixed $interfaceVersion
     */
    public function setInterfaceVersion($interfaceVersion)
    {
        $this->interfaceVersion = $interfaceVersion;
    }

    public function getData()
    {
        foreach ($this->getRequiredCoreFields() as $field) {
            $this->validate($field);
        }
        $this->validateCardFields();
        return $this->getBaseData() + $this->getTransactionData();
    }

    public function validateCardFields()
    {
        $card = $this->getCard();
        foreach ($this->getRequiredCardFields() as $field) {
            $fn = 'get' . ucfirst($field);
            $result = $card->$fn();
            if (empty($result)) {
                throw new InvalidRequestException("The $field parameter is required");
            }
        }
    }
    public function getMerchantID()
    {
        return $this->getParameter('merchant_id');
    }

    public function setMerchantID($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secret_key');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secret_key', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getSeal($data)
    {
        return hash('sha256', $data . $this->getSecretKey());
    }
}
