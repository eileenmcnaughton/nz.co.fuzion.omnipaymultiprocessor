<?php

namespace Omnipay\Paypalstandard\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
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

  /**
   * Generate a signature using a secret key.
   *
   * @param array $data
   * @return string
   */
  public function generateSignature($data)
  {
    $msg = array();
    foreach ($data as $key => $value) {
      $msg[] = "{$key}={$value}";
    }
    // If the key is in ASCII format, convert it to binary
    $binKey = pack("H*", $this->getKey());
    return strtoupper(hash_hmac('sha512', implode('&', $msg), $binKey));
  }

  public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

  public function getSite()
  {
    return $this->getParameter('site');
  }

  public function setSite($value)
  {
    return $this->setParameter('site', $value);
  }

  public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

  public function getMerchantAccountEmail()
  {
    return $this->getParameter('MerchantAccountEmail');
  }

  public function setMerchantAccountEmail($value)
  {
    return $this->setParameter('MerchantAccountEmail', $value);
  }

  public function getRequiredFields()
  {
    return array_merge($this->getRequiredCardFields(), $this->getRequiredCardFields());
  }

}
