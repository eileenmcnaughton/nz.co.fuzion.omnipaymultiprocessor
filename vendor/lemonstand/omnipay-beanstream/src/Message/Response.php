<?php namespace Omnipay\Beanstream\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = json_decode($data, true);
    }

    public function isSuccessful()
    {
        return (isset($this->data['approved']) && $this->data['approved'] === "1");
    }

    public function getTransactionReference()
    {
        return isset($this->data['id']) ? $this->data['id'] : null;
    }

    public function getType()
    {
        return isset($this->data['type']) ? $this->data['type'] : null;
    }

    public function getOrderNumber()
    {
        return isset($this->data['order_number']) ? $this->data['order_number'] : null;
    }

    public function getMessageId()
    {
        return isset($this->data['message_id']) ? $this->data['message_id'] : null;
    }

    public function getMessage()
    {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }

    public function getAuthCode()
    {
        return isset($this->data['auth_code']) ? $this->data['auth_code'] : null;
    }

    public function getCode()
    {
        return isset($this->data['code']) ? $this->data['code'] : null;
    }

    public function getCard()
    {
        return isset($this->data['card']) ? $this->data['card'] : null;
    }

    public function getReference()
    {
        return isset($this->data['reference']) ? $this->data['reference'] : null;
    }

    public function getCategory()
    {
        return isset($this->data['category']) ? $this->data['category'] : null;
    }
}
