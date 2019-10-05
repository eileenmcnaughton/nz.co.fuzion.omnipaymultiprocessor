<?php namespace Omnipay\Beanstream\Message;

class FetchProfileCardsRequest extends FetchProfileRequest
{
    public function getEndpoint()
    {
        return $this->endpoint . '/' . $this->getProfileId() . '/cards';
    }
}
