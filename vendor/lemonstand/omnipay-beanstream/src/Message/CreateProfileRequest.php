<?php namespace Omnipay\Beanstream\Message;

class CreateProfileRequest extends AbstractProfileRequest
{
    public function getData()
    {
        $data = array(
            'language' => $this->getLanguage(),
            'comment' => $this->getComment(),
            'billing' => $this->getBilling()
        );

        if ($this->getCard()) {
            $this->getCard()->validate();

            $data['card'] = array(
                'number' => $this->getCard()->getNumber(),
                'name' => $this->getCard()->getName(),
                'expiry_month' => $this->getCard()->getExpiryDate('m'),
                'expiry_year' => $this->getCard()->getExpiryDate('y'),
                'cvd' => $this->getCard()->getCvv(),
            );

            $billing = $this->getBilling();

            if (empty($billing)) {
                $data['billing'] = array(
                    'name' => $this->getCard()->getBillingName(),
                    'address_line1' => $this->getCard()->getBillingAddress1(),
                    'address_line2' => $this->getCard()->getBillingAddress2(),
                    'city' => $this->getCard()->getBillingCity(),
                    'province' => $this->getCard()->getBillingState(),
                    'country' => $this->getCard()->getBillingCountry(),
                    'postal_code' => $this->getCard()->getBillingPostcode(),
                    'phone_number' => $this->getCard()->getBillingPhone(),
                    'email_address' => $this->getCard()->getEmail(),
                );
            }
        }

        if ($this->getToken()) {
            $data['token'] = $this->getToken();
        }

        return $data;
    }

    public function getHttpMethod()
    {
        return 'POST';
    }
}
