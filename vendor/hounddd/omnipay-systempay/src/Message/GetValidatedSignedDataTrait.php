<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Common\Exception\InvalidResponseException;

trait GetValidatedSignedDataTrait
{
    public function getData()
    {
        $signature = $this->generateSignature($this->httpRequest->request->all());
        if (strtolower($this->httpRequest->request->get('signature')) !== strtolower($signature)) {
            throw new InvalidResponseException('Invalid signature');
        }

        return $this->httpRequest->request->all();
    }
}
