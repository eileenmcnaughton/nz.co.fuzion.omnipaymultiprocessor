<?php

namespace Omnipay\BitPay\Message;

/**
 * BitPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = array();
        $data['price'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['posData'] = $this->getTransactionId();
        $data['itemDesc'] = $this->getDescription();
        $data['notificationURL'] = $this->getNotifyUrl();
        $data['redirectURL'] = $this->getReturnUrl();

        return $data;
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/invoice';
    }
}
