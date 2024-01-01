<?php

namespace Omnipay\NABTransact\Message;

class DirectPostWebhookRequest extends DirectPostAbstractRequest
{
    private $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function generateResponseFingerprint($data)
    {
        $hashable = [
            $data['merchant'],
            $data['txn_password'],
            $data['refid'],
            $data['amount'],
            $data['timestamp'],
            $data['summarycode'],
        ];

        $hash = implode('|', $hashable);

        return hash_hmac('sha256', $hash, $data['txn_password']);
    }

    public function vefiyFingerPrint($fingerprint)
    {
        $data = $this->data;

        if ($fingerprint !== $this->generateResponseFingerprint($data)) {
            $data['restext'] = $data['restext'].', Invalid fingerprint.';
            $data['summarycode'] = 3;
        }

        return new DirectPostCompletePurchaseResponse($this, $data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendData($data)
    {
    }
}
