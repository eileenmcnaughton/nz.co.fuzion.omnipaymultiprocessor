<?php

namespace Omnipay\Cybersource\Message;

/**
 * Stripe Update Credit Card Request
 */
class UpdateCardRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();
        $data['description'] = $this->getDescription();

        if ($this->getToken()) {
            $data['card'] = $this->getToken();
        } elseif ($this->getCard()) {
            $data['card'] = $this->getCardData();
            $data['email'] = $this->getCard()->getEmail();
        }

        $this->validate('cardReference');

        return $data;
    }

    public function getEndpoint()
    {
        return parent::$endpoint . '/token/update';
    }
}
