<?php

namespace Omnipay\BitPay\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://bitpay.com/api';
    protected $testEndpoint = 'https://test.bitpay.com/api';

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    protected function getHttpMethod()
    {
        return 'POST';
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function sendData($data)
    {
        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            array('Authorization' => 'Basic '.base64_encode($this->getApiKey().':')),
            $data
        );

        $httpResponse = $httpRequest->send();

        return $this->response = $this->createResponse($httpResponse->json(), $httpResponse->getStatusCode());
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new PurchaseResponse($this, $data, $statusCode);
    }
}
