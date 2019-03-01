<?php

namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Delete Card Request
*/
class DirectPostDeleteCardRequest extends AbstractRequest
{
    protected $customer_vault = 'delete_customer';

    public function getData()
    {
        $this->validate('cardReference');

        $data = $this->getBaseData();

        $data['customer_vault_id'] = $this->getCardReference();

        return $data;
    }
}
