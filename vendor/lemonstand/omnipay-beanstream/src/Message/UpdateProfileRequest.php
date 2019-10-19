<?php namespace Omnipay\Beanstream\Message;

class UpdateProfileRequest extends CreateProfileRequest
{
    public function getData()
    {
        $this->validate('profile_id');
        return parent::getData();
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/' . $this->getProfileId();
    }

    public function getHttpMethod()
    {
        return 'PUT';
    }
}
