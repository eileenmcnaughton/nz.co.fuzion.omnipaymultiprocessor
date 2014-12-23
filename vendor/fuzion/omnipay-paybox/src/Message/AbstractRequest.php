<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize.Net Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    public function validateCardFields()
    {
        $card = $this->getCard();
        foreach ($this->getRequiredCardFields() as $field) {
            $fn = 'get' . ucfirst($field);
            $value = $card->$fn();
            if ($value === null) {
                throw new InvalidRequestException("The $field parameter is required");
            }
        }
    }

    /**
     * Generate a signature using a secret key
     * @param $data
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

    public function getSite()
    {
        return $this->getParameter('site');
    }

    public function setSite($value)
    {
        return $this->setParameter('site', $value);
    }

    public function getKey()
    {
        return $this->getParameter('key');
    }

    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    public function getRang()
    {
        return $this->getParameter('rang');
    }

    public function setRang($value)
    {
        return $this->setParameter('rang', $value);
    }

    public function getIdentifiant()
    {
        return $this->getParameter('identifiant');
    }

    public function setIdentifiant($value)
    {
        return $this->setParameter('identifiant', $value);
    }

    public function getRequiredFields()
    {
        return array_merge($this->getRequiredCardFields(), $this->getRequiredCardFields());
    }
}
