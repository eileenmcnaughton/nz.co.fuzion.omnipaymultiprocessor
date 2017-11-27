<?php
namespace Omnipay\pergo\Message;

/**
 * Authorize Request
 */
class OffsiteCompletePurchaseRequest extends OffsiteAbstractRequest
{
    public function sendData($data)
    {
        return $this->response = new OffsiteCompletePurchaseResponse($this, $data);
    }

    public function getData()
    {
        return $this->httpRequest->request->all();
    }
}
