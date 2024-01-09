<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * NABTransact Direct Post Complete Purchase Request.
 */
class DirectPostCompletePurchaseRequest extends DirectPostAbstractRequest
{
    /**
     * @return array|Exception
     */
    public function getData()
    {
        $data = $this->httpRequest->query->all();

        if ($this->generateResponseFingerprint($data) !== $this->httpRequest->query->get('fingerprint')) {
            throw new InvalidRequestException('Invalid fingerprint');
        }

        return $data;
    }

    /**
     * @param $data
     */
    public function generateResponseFingerprint($data)
    {
        $hashable = [
            $data['merchant'],
            $this->getTransactionPassword(),
            $data['refid'],
            $this->getAmount(),
            $data['timestamp'],
            $data['summarycode'],
        ];

        $hash = implode('|', $hashable);

        return hash_hmac('sha256', $hash, $this->getTransactionPassword());
    }

    /**
     * @param $data
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new DirectPostCompletePurchaseResponse($this, $data);
    }
}
