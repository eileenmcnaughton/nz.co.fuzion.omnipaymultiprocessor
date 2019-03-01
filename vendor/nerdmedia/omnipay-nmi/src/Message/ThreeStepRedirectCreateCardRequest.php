<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Create Card Request
 */
class ThreeStepRedirectCreateCardRequest extends ThreeStepRedirectAbstractRequest
{
    /**
     * @var string
     */
    protected $type = 'add-customer';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('card');

        $data = array(
            'api-key'      => $this->getApiKey(),
            'redirect-url' => $this->getRedirectUrl(),
        );

        $data = array_merge(
            $data,
            $this->getBillingData(),
            $this->getShippingData()
        );

        return $data;
    }
}
