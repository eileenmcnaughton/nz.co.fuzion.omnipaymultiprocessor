<?php
namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Capture Request
*/
class DirectPostCaptureRequest extends AbstractRequest
{
    protected $type = 'capture';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data['transactionid'] = $this->getTransactionReference();

        if ($this->getAmount() > 0) {
            $data['amount'] = $this->getAmount();
        }

        return $data;
    }
}
