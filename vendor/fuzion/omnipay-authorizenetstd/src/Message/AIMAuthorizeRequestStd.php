<?php

namespace Omnipay\AuthorizeNetStd\Message;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMAuthorizeRequestStd extends AIMAuthorizeRequest
{

  public function sendData($data)
  {
    $headers = array('Content-Type' => 'text/xml; charset=utf-8');

    $data = $data->saveXml();
    $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

    return $this->response = new AIMResponseStd($this, $httpResponse->getBody());
  }

  public function setTransactionReference($value)
  {
    return $this->setParameter('transactionReference', $value);
  }

}
