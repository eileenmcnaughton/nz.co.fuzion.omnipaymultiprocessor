<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Delete Card Request
 */
class ThreeStepRedirectDeleteCardRequest extends ThreeStepRedirectAbstractRequest
{
    /**
     * @var string
     */
    protected $type = 'delete-customer';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('cardReference');

        $data = array(
            'api-key'           => $this->getApiKey(),
            'redirect-url'      => $this->getRedirectUrl(),
            'customer-vault-id' => $this->getCardReference(),
        );

        return $data;
    }
}
