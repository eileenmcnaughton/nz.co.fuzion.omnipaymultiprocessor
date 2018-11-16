<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Abstract Request.
 */
abstract class DirectPostAbstractRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint = 'https://demo.transact.nab.com.au/directpostv2/authorise';

    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/live/directpostv2/authorise';

    /**
     * @param array $data
     */
    public function generateFingerprint(array $data)
    {
        $hash = implode('|', [
            $data['EPS_MERCHANT'],
            $this->getTransactionPassword(),
            $data['EPS_TXNTYPE'],
            $data['EPS_REFERENCEID'],
            $data['EPS_AMOUNT'],
            $data['EPS_TIMESTAMP'],
        ]);

        return sha1($hash);
    }

    /**
     * @return mixed
     */
    public function getBaseData()
    {
        $data = [];

        $data['EPS_MERCHANT'] = $this->getMerchantId();
        $data['EPS_TXNTYPE'] = $this->txnType;
        $data['EPS_IP'] = $this->getClientIp();
        $data['EPS_AMOUNT'] = $this->getAmount();
        $data['EPS_REFERENCEID'] = $this->getTransactionId();
        $data['EPS_TIMESTAMP'] = gmdate('YmdHis');
        $data['EPS_FINGERPRINT'] = $this->generateFingerprint($data);
        $data['EPS_RESULTURL'] = $this->getReturnUrl();
        $data['EPS_CALLBACKURL'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['EPS_REDIRECT'] = 'TRUE';
        $data['EPS_CURRENCY'] = $this->getCurrency();

        return $data;
    }
}
