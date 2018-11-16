<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * NABTransact Direct Post Complete Purchase Request.
 */
class DirectPostCompletePurchaseRequest extends DirectPostAbstractRequest
{
    /**
     * @return mixed
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
        $fields = implode('|', [
            $data['merchant'],
            $this->getTransactionPassword(),
            $data['refid'],
            $this->getAmount(),
            $data['timestamp'],
            $data['summarycode'],
        ]);

        return sha1($fields);
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function sendData($data)
    {
        return $this->response = new DirectPostCompletePurchaseResponse($this, $data);
    }
}
