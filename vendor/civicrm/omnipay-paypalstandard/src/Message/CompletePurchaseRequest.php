<?php

namespace Omnipay\Paypalstandard\Message;

/**
 * Authorize Request
 * CompletePurchaseRequest.php - processes the IPN
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    public function getData()
    {
        return $this->httpRequest->request->all();
    }
}
