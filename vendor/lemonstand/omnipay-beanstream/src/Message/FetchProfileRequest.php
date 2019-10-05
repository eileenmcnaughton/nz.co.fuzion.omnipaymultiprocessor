<?php namespace Omnipay\Beanstream\Message;

class FetchProfileRequest extends AbstractProfileRequest
{
    public function getData()
    {
        $this->validate('profile_id');
        return;
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/' . $this->getProfileId();
    }

    public function getHttpMethod()
    {
        return 'GET';
    }
}
