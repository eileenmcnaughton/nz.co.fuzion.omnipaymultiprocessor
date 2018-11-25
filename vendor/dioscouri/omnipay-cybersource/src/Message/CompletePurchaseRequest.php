<?php

namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Cybersource Purchase Request
 */
class CompletePurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = $this->httpRequest->request->all();
        if ($this->generateSignature(
            $data,
            explode(',', $data['signed_field_names']),
            $this->getSecretKey()
        )
            != $data['signature']) {
            throw new InvalidRequestException('signature mismatch');
        }
        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
