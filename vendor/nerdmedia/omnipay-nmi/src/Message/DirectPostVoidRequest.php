<?php
namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Void Request
*/
class DirectPostVoidRequest extends AbstractRequest
{
    protected $type = 'void';

    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data['transactionid'] = $this->getTransactionReference();

        return $data;
    }
}
