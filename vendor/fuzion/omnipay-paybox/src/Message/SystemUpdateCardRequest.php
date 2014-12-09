<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Create Credit Card Request
 */
class SystemUpdateCardRequest extends SystemAuthorizeRequest
{
    public function getData()
    {
        $data = array();
        $data['description'] = $this->getDescription();

        if ($this->getToken()) {
            $data['PORTEUR'] = $this->getToken();
        } elseif ($this->getCard()) {
            $data['PORTEUR'] = $this->getCardData();
            $data['email'] = $this->getCard()->getEmail();
        } else {
            // one of token or card is required
            $this->validate('card');
        }

        return $data;
    }

    public function getTransactionType()
    {
        return '00056';
    }
}
