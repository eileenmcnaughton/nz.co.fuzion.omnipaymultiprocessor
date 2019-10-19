<?php

namespace Omnipay\Beanstream\Message;

/**
 * Class VoidRequest
 *
 * @package Omnipay\Beanstream\Message
 */
class VoidRequest extends AbstractRequest
{

    /**
     * Get the data necessary for a Void
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        return array(
            'amount'=>$this->getAmount()
        );
    }

    /**
     * Get the endpoint for a Void. This is overwriting the method so we can add the transaction reference dynamically
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint . '/payments/' . $this->getTransactionReference() . '/void';
    }
}
