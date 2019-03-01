<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Capture Request
 */
class ThreeStepRedirectCaptureRequest extends ThreeStepRedirectAbstractRequest
{
    /**
     * @var string
     */
    protected $type = 'capture';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('transactionReference');

        $data = array(
            'api-key'        => $this->getApiKey(),
            'transaction-id' => $this->getTransactionReference(),
        );

        if ($this->getAmount() > 0) {
            $data['amount'] = $this->getAmount();
        }

        if ($this->getMerchantDefinedField1()) {
            $data['merchant-defined-field-1'] = $this->getMerchantDefinedField1();
        }

        if ($this->getMerchantDefinedField2()) {
            $data['merchant-defined-field-2'] = $this->getMerchantDefinedField2();
        }

        if ($this->getMerchantDefinedField3()) {
            $data['merchant-defined-field-3'] = $this->getMerchantDefinedField3();
        }

        if ($this->getMerchantDefinedField4()) {
            $data['merchant-defined-field-4'] = $this->getMerchantDefinedField4();
        }

        return $data;
    }
}
