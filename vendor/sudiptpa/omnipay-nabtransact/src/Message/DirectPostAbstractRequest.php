<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Enums\TransactionType;

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
        $hashable = [
            $data['EPS_MERCHANT'],
            $this->getTransactionPassword(),
            $data['EPS_TXNTYPE'],
            $data['EPS_REFERENCEID'],
            $data['EPS_AMOUNT'],
            $data['EPS_TIMESTAMP'],
        ];

        if ($this->getHasEMV3DSEnabled()) {
            $hashable = array_merge(
                $hashable,
                [$data['EPS_ORDERID']]
            );
        }

        $hash = implode('|', $hashable);

        return hash_hmac('sha256', $hash, $this->getTransactionPassword());
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        $data = [];

        $data['EPS_MERCHANT'] = $this->getMerchantId();
        $data['EPS_TXNTYPE'] = $this->txnType;
        $data['EPS_REFERENCEID'] = $this->getTransactionId();
        $data['EPS_AMOUNT'] = $this->getAmount();
        $data['EPS_TIMESTAMP'] = gmdate('YmdHis');
        $data['EPS_RESULTURL'] = $this->getReturnUrl();
        $data['EPS_IP'] = $this->getClientIp();
        $data['EPS_REDIRECT'] = 'TRUE';

        if ($this->getNotifyUrl()) {
            $data['EPS_CALLBACKURL'] = $this->getNotifyUrl();
        }

        if ($currency = $this->getCurrency()) {
            $data['EPS_CURRENCY'] = $currency;
        }

        $card = $this->getCard();

        if ($billingPostcode = $card->getBillingPostcode()) {
            $data['EPS_ZIPCODE'] = $billingPostcode;
        }

        if ($billingCity = $card->getBillingCity()) {
            $data['EPS_TOWN'] = $billingCity;
        }

        if ($billingCountry = $card->getBillingCountry()) {
            $data['EPS_BILLINGCOUNTRY'] = $billingCountry;
        }

        if ($shippingCountry = $card->getShippingCountry()) {
            $data['EPS_DELIVERYCOUNTRY'] = $shippingCountry;
        }

        if ($emailAddress = $card->getEmail()) {
            $data['EPS_EMAILADDRESS'] = $emailAddress;
        }

        if ($this->getHasEMV3DSEnabled()) {
            $data['EPS_ORDERID'] = $this->getTransactionReference();

            $data['EPS_TXNTYPE'] = TransactionType::PAYMENT_3DS_EMV3DS;
        }

        $data['EPS_FINGERPRINT'] = $this->generateFingerprint($data);

        return $data;
    }
}
