<?php
namespace Omnipay\Pergo\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Request
 */
abstract class OffsiteAbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
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
    public function getAuthenticationToken()
    {
        return $this->getParameter('authenticationToken');
    }

    public function setAuthenticationToken($value)
    {
        return $this->setParameter('authenticationToken', $value);
    }

    public function getBillerAccountId()
    {
        return $this->getParameter('billerAccountId');
    }

    public function setBillerAccountId($value)
    {
        return $this->setParameter('billerAccountId', $value);
    }

    public function getMerchantProfileId()
    {
        return $this->getParameter('merchantProfileId');
    }

    public function setMerchantProfileId($value)
    {
        return $this->setParameter('merchantProfileId', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }
}
