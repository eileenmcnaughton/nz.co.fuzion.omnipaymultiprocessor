<?php

namespace Omnipay\Beanstream\Message;

/**
 * Class RefundRequest
 *
 * @package Omnipay\Beanstream\Message
 */
class RefundRequest extends AbstractRequest
{

    /**
     * Get the data for a refund
     */
    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        return array(
            'amount'=>$this->getAmount(),
            'order_number'=>$this->getOrderNumber()
        );
    }

    /**
     * Get the endpoint for a Refund. This is overwriting the method so we can add the transaction reference dynamically
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint . '/payments/' . $this->getTransactionReference() . '/returns';
    }
}
