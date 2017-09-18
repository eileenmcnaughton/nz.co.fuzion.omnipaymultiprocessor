<?php
/**
 * eWAY Rapid Void Request
 */
 
namespace Omnipay\Eway\Message;

class RapidDirectVoidRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');

        $data = array();
        $data['TransactionId'] = $this->getTransactionReference();
        
        return $data;
    }

    protected function getEndpoint()
    {
        return $this->getEndpointBase().'/CancelAuthorisation';
    }

    public function sendData($data)
    {
        // This request uses the REST endpoint and requires the JSON content type header
        $httpResponse = $this->httpClient->post(
            $this->getEndpoint(),
            array('content-type' => 'application/json'),
            json_encode($data)
        )
        ->setAuth($this->getApiKey(), $this->getPassword())
        ->send();
        return $this->response = new RapidResponse($this, $httpResponse->json());
    }
}
