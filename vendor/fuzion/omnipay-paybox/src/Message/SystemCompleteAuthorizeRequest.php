<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Paybox Authorize Request
 */
class SystemCompleteAuthorizeRequest extends AbstractRequest
{

    /**
     * Data signature from paybox.
     *
     * @var
     */
    protected $signature;

    public function getData()
    {
        $this->data = $this->httpRequest->query->all();
        return $this->data;
    }

    public function sendData($data)
    {
        return $this->response = new SystemCompleteAuthorizeResponse($this, $data);
    }
}
