<?php

namespace Omnipay\Paybox\Message;

/**
 * Paybox Authorize Request
 */
class SystemCompleteAuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        if (strtolower($this->httpRequest->request->get('x_MD5_Hash')) !== $this->getHash()) {
            throw new InvalidRequestException('Incorrect hash');
        }

        return $this->httpRequest->request->all();
    }

    public function sendData($data)
    {
        return $this->response = new SystemCompleteAuthorizeResponse($this, $data);
    }
}
