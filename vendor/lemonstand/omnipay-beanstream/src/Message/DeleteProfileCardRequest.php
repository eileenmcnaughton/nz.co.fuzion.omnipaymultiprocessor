<?php namespace Omnipay\Beanstream\Message;

class DeleteProfileCardRequest extends DeleteProfileRequest
{
    public function getData()
    {
        $this->validate('profile_id');
        $this->validate('card_id');
        return;
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/' . $this->getProfileId() . '/cards/' . $this->getCardId();
    }
}
