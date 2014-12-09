<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox System Authorize Request
 */
class SystemAuthorizeRequest extends AbstractRequest
{

    public function getData()
    {
        foreach ($this->getRequiredCoreFields() as $field) {
            $this->validate($field);
        }
        $this->validateCardFields();
        $data = $this->getBaseData() + $this->getTransactionData();
        $data['PBX_HMAC'] = $this->generateSignature($data);
        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new SystemResponse($this, $data, $this->getEndpoint());
    }

    protected function createResponse($data)
    {
        return $this->response = new SystemResponse($this, $data);
    }

    public function getSite()
    {
        return $this->getParameter('site');
    }

    public function setSite($value)
    {
        return $this->setParameter('site', $value);
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

    public function getRequiredCoreFields()
    {
        return array
        (
            'amount',
            'currency',
        );
    }

    public function getRequiredCardFields()
    {
        return array
        (
            'email',
        );
    }

    public function getTransactionData()
    {
        return array
        (
            'PBX_TOTAL' => $this->getAmountInteger(),
            'PBX_DEVISE' => $this->getCurrencyNumeric(),
            'PBX_CMD' => $this->getTransactionId(),
            'PBX_PORTEUR' => $this->getCard()->getEmail(),
            'PBX_RETOUR' => 'Mt:M;Ref:R;Auto:A;Erreur:E',
            'PBX_HASH' => 'SHA512',
            'PBX_TIME' => date("c"),
        );
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        return array(
            'PBX_SITE' => $this->getSite(),
            'PBX_RANG' => $this->getRang(),
            'PBX_IDENTIFIANT' => $this->getIdentifiant(),
        );
    }

    /**
     * @return string
     */
    public function getUniqueID()
    {
        return uniqid();
    }

    /**
     * @return string
     * http://www1.paybox.com/wp-content/uploads/2014/02/ManuelIntegrationPayboxSystem_V6.2_EN.pdf
     */
    public function getEndpoint()
    {
        return 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
    }

    public function getPaymentMethod()
    {
        return 'card';
    }

    public function getTransactionType()
    {
        return '00001';
    }
}
