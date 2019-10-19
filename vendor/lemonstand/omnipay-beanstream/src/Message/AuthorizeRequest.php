<?php namespace Omnipay\Beanstream\Message;

class AuthorizeRequest extends AbstractRequest
{
    protected $complete = false;

    public function getEndpoint()
    {
        return $this->endpoint . '/payments';
    }

    public function getData()
    {
        $this->validate('amount');

        $data = array(
            'amount' => $this->getAmount(),
            'order_number' => $this->getOrderNumber(),
            'language' => $this->getLanguage(),
            'customer_ip' => $this->getClientIp(),
            'term_url' => $this->getTermUrl(),
            'comments' => $this->getComments(),
            'payment_method' => $this->getPaymentMethod(),
            'billing' => $this->getBilling(),
            'shipping' => $this->getShipping()
        );

        if ($this->getCard()) {
            $this->getCard()->validate();

            $data['card'] = array(
                'number' => $this->getCard()->getNumber(),
                'name' => $this->getCard()->getName(),
                'expiry_month' => $this->getCard()->getExpiryDate('m'),
                'expiry_year' => $this->getCard()->getExpiryDate('y'),
                'cvd' => $this->getCard()->getCvv(),
                'complete' => $this->complete,
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

            $shipping = $this->getShipping();

            if (empty($shipping)) {
                $data['shipping'] = array(
                    'name' => $this->getCard()->getShippingName(),
                    'address_line1' => $this->getCard()->getShippingAddress1(),
                    'address_line2' => $this->getCard()->getShippingAddress2(),
                    'city' => $this->getCard()->getShippingCity(),
                    'province' => $this->getCard()->getShippingState(),
                    'country' => $this->getCard()->getShippingCountry(),
                    'postal_code' => $this->getCard()->getShippingPostcode(),
                    'phone_number' => $this->getCard()->getShippingPhone(),
                    'email_address' => $this->getCard()->getEmail(),
                );
            }
        }

        if ($this->getPaymentProfile()) {
            $data['payment_profile'] = $this->getPaymentProfile();
            $data['payment_profile']['complete'] = $this->complete;
        }

        if ($this->getToken()) {
            $data['token'] = $this->getToken();
            $data['token']['complete'] = $this->complete;
        }

        return $data;
    }
}
