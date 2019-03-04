<?php

namespace Omnipay\NABTransact\Message;

/**
 * UnionPayPurchaseRequest.
 */
class UnionPayPurchaseRequest extends DirectPostAbstractRequest
{
    /**
     * @var string
     */
    public $txnType = '0';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'transactionId');

        $data = $this->getBaseData();

        $data['EPS_PAYMENTCHOICE'] = 'UPOP';

        return $data;
    }

    /**
     * @param $data
     *
     * @return \Omnipay\NABTransact\Message\UnionPayPurchaseResponse
     */
    public function sendData($data)
    {
        $redirectUrl = $this->getEndpoint().'?'.http_build_query($data);

        return $this->response = new UnionPayPurchaseResponse($this, $data, $redirectUrl);
    }
}
