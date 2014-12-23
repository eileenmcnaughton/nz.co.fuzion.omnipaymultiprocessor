<?php
namespace Omnipay\Cybersource\Message;

/**
 *
 * @author Rafael Diaz-Tushman
 *
 */
class CompleteAuthorizeRequest extends AbstractRequest
{

    public function getData()
    {
        $data = $this->httpRequest->request->all();

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new CompleteAuthorizeResponse($this, $data);
    }
}
