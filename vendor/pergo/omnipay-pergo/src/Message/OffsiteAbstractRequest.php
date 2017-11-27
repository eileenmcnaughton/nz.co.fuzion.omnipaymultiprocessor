<?php
namespace Omnipay\pergo\Message;

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
        return $this->getParameter('authenticationtoken');
    }

    public function setAuthenticationToken($value)
    {
        return $this->setParameter('authenticationtoken', $value);
    }

    public function getBillerAccountId()
    {
        return $this->getParameter('billeraccountid');
    }

    public function setBillerAccountId($value)
    {
        return $this->setParameter('billeraccountid', $value);
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
