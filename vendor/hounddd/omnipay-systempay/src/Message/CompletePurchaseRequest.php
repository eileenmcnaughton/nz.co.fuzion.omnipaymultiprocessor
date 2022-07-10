<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * SystemPay Complete Purchase Request
 */
class CompletePurchaseRequest extends AbstractRequest
{

    use GetValidatedSignedDataTrait;

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
    
    public function getEndpoint()
    {
        return 'https://paiement.systempay.fr/vads-payment/';
    }
}
