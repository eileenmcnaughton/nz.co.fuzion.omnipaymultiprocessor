<?php namespace Omnipay\Beanstream\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class ProfileResponse extends Response
{
    public function isSuccessful()
    {
        return (isset($this->data['message']) && $this->data['message'] === "Operation Successful")
         && (isset($this->data['code']) && $this->data['code'] === 1);
    }

    public function getCustomerCode()
    {
        return isset($this->data['customer_code']) ? $this->data['customer_code'] : null;
    }
}
