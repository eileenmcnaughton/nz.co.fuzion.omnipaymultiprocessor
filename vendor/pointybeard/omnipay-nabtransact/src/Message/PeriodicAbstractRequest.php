<?php

namespace Omnipay\NABTransact\Message;

abstract class PeriodicAbstractRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://transact.nab.com.au/xmlapi/periodic';
    protected $testEndpoint = 'https://transact.nab.com.au/xmlapidemo/periodic';

    abstract protected function buildRequestBody(array $data);

    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    protected function getBaseData()
    {
        $data = parent::getBaseData();
        $data['CustomerReferenceNumber'] = $this->getCustomerReference();

        //if (false == ($data['Customer']['CustomerReferenceNumber'] = $this->getCardReference())) {
        //    $this->validate('card');
        //}

        if ($this->getCard()) {
            $data['Customer']['CardDetails'] = [
                'ExpiryMonth' => $this->getCard()->getExpiryDate('m'),
                'ExpiryYear' => $this->getCard()->getExpiryDate('y'),
                'Number' => $this->getCard()->getNumber(),
                'Cvv' => $this->getCard()->getCvv(),
            ];
        }

        return $data;
    }

    public function sendData($data)
    {
        $this->setMessageTimestamp(self::generateMessageTimestamp());
        $data['MessageTimestamp'] = $this->getMessageTimestamp();

        $httpResponse = $this->httpClient
            ->post($this->getEndpoint(), null, $this->buildRequestBody($data))
            ->send();

        return $this->response = new PeriodicResponse($this, $httpResponse->getBody());
    }

    public function getEndpoint()
    {
        return self::getEndpointBase();
    }
}
