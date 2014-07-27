<?php

namespace Omnipay\Cybersource\Message;

/**
 * Cybersource Purchase Request
 */
class CompletePurchaseRequest extends AuthorizeRequest
{
  public function getData()
  {
    $data = $this->httpRequest->request->all();
    return $data;
  }
  public function sendData($data)
  {
    return $this->response = new CompletePurchaseResponse($this, $data);
  }
}
