<?php

namespace Omnipay\NABTransact\Message;

/**
 * HostedPayment Purchase Request.
 */
class HostedPaymentPurchaseRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/test/hpp/payment';

    /**
     * @var string
     */
    public $testEndpoint = 'https://transact.nab.com.au/live/hpp/payment';

    /**
     * @return mixed
     */
    public function getData()
    {
        $this->validate(
            'amount',
            'returnUrl',
            'transactionId',
            'merchantId',
            'paymentAlertEmail'
        );

        $data = [];

        $data['vendor_name'] = $this->getMerchantId();
        $data['payment_alert'] = $this->getPaymentAlertEmail();
        $data['payment_reference'] = $this->getTransactionId();
        $data['currency'] = $this->getCurrency();
        $data['return_link_url'] = $this->getReturnUrl();
        $data['reply_link_url'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['return_link_text'] = $this->getReturnUrlText();
        $data['total_amount'] = $this->getAmount();

        return $data;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @return mixed
     */
    public function getPaymentAlertEmail()
    {
        return $this->getParameter('paymentAlertEmail');
    }

    /**
     * @return mixed
     */
    public function getReturnUrlText()
    {
        return $this->getParameter('returnUrlText');
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function sendData($data)
    {
        return $this->response = new HostedPaymentPurchaseResponse($this, $data, $this->getEndpoint());
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setPaymentAlertEmail($value)
    {
        return $this->setParameter('paymentAlertEmail', $value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setReturnUrlText($value)
    {
        return $this->setParameter('returnUrlText', $value);
    }
}
