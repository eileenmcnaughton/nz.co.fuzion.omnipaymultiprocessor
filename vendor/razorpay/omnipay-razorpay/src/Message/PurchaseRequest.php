<?php

namespace Omnipay\Razorpay\Message;

use \Omnipay\Common\Message\AbstractRequest;

/**
 * Razorpay Complete Purchase Request - Auto Capture on by default. For Off-Site payment.
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        if (!empty($this->getCard())) {
            $card = $this->getCard();

            $hmacKey = $this->getKeySecret();

            $data = array(
                    'x_account_id'                   => $this->getKeyID(),
                    'x_amount'                       => $this->getAmount(), // in paise
                    'x_currency'                     => $this->getCurrency(),
                    'x_customer_email'               => $card->getEmail(),
                    'x_customer_first_name'          => $card->getFirstName(),
                    'x_customer_last_name'           => $card->getLastName(),
                    'x_customer_billing_country'     => $card->getCountry(),
                    'x_customer_billing_city'        => $card->getCity(),
                    'x_customer_billing_address'     => $card->getAddress1(),
                    'x_customer_billing_state'       => $card->getState(),
                    'x_customer_billing_zip'         => $card->getPostcode(),
                    'x_customer_shipping_address1'   => $card->getAddress1(),
                    'x_customer_shipping_city'       => $card->getCity(),
                    'x_customer_shipping_country'    => $card->getCountry(),
                    'x_customer_shipping_first_name' => $card->getFirstName(),
                    'x_customer_shipping_last_name'  => $card->getLastName(),
                    'x_customer_shipping_state'      => $card->getState(),
                    'x_customer_shipping_zip'        => $card->getPostcode(),
                    'x_description'                  => $this->getDescription(),
                    'x_invoice'                      => '#' . $this->getTransactionId(),
                    'x_reference'                    => $this->getTransactionId(),
                    'x_shop_country'                 => $card->getCountry(),
                    'x_signature'                    => '',
                    'x_test'                         => false, // set in .env
                    'x_url_callback'                 => $this->getReturnUrl(),
                    'x_url_cancel'                   => $this->getCancelUrl(),
                    'x_url_complete'                 => $this->getReturnUrl()
                );

            $razorpaySignature = new Signature($hmacKey);
            $signature = $razorpaySignature->getSignature($data);

            $data['x_signature'] = $signature;

            return $data;
        }

        // Default case
        return $this->getParameters();
    }

    // To send the data for our
    public function sendData($data)
    {
        return $this->createResponse($data);
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function getKeyID()
    {
        return $this->getParameter('key_id');
    }

    public function setKeyID($value)
    {
        return $this->setParameter('key_id', $value);
    }

    public function getKeySecret()
    {
        return $this->getParameter('key_secret');
    }

    public function setKeySecret($value)
    {
        return $this->setParameter('key_secret', $value);
    }
}
