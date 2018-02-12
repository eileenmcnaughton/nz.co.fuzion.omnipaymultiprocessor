<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox System Authorize Request
 */
class SystemAuthorizeRequest extends AbstractRequest
{

    /**
     * Transaction time in timezone format e.g 2011-02-28T11:01:50+01:00.
     *
     * @var string
     */
    protected $time;

    /**
     * Get time of the transaction.
     *
     * @return string
     */
    public function getTime()
    {
        return (!empty($this->time)) ? $this->time : date('c');
    }

    /**
     * Setter for time (of transaction).
     *
     * @param string $time
     *  Time in 'c' format - e.g 2011-02-28T11:01:50+01:00
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getData()
    {
        foreach ($this->getRequiredCoreFields() as $field) {
            $this->validate($field);
        }
        $this->validateCardFields();
        $data = $this->getBaseData() + $this->getTransactionData() + $this->getURLData();
        $data['PBX_HMAC'] = $this->generateSignature($data);
        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new SystemResponse($this, $data, $this->getEndpoint());
    }

    protected function createResponse($data)
    {
        return $this->response = new SystemResponse($this, $data, $this->getEndpoint());
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
            'PBX_RETOUR' => 'Mt:M;Id:R;Ref:A;Erreur:E;sign:K',
            'PBX_TIME' => $this->getTime(),
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
     * Get values for IPN and browser return urls.
     *
     * Browser return urls should all be set or non set.
     */
    public function getURLData()
    {
        $data = array();
        if ($this->getNotifyUrl()) {
            $data['PBX_REPONDRE_A'] = $this->getNotifyUrl();
        }
        if ($this->getReturnUrl()) {
            $data['PBX_EFFECTUE'] = $this->getReturnUrl();
            $data['PBX_REFUSE'] = $this->getReturnUrl();
            $data['PBX_ANNULE'] = $this->getCancelUrl();
            $data['PBX_ATTENTE'] = $this->getReturnUrl();
        }
        return $data;
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
        if ($this->getTestMode()) {
            return 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
        } else {
            return 'https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
        }
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
