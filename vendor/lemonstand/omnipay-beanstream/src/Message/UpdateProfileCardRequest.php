<?php namespace Omnipay\Beanstream\Message;

class UpdateProfileCardRequest extends CreateProfileCardRequest
{
    public function getData()
    {
        $this->validate('profile_id');
        $this->validate('card_id');
        return parent::getData();
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/' . $this->getProfileId() . '/cards/' . $this->getCardId();
    }

    public function getHttpMethod()
    {
        return 'PUT';
    }
}
