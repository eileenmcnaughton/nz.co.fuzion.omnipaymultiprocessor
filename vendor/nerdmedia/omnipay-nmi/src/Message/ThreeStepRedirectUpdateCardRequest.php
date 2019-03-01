<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Update Card Request
 */
class ThreeStepRedirectUpdateCardRequest extends ThreeStepRedirectCreateCardRequest
{
    /**
     * @var string
     */
    protected $type = 'update-customer';

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();

        $this->validate('cardReference');

        $data['customer-vault-id'] = $this->getCardReference();

        return $data;
    }
}
