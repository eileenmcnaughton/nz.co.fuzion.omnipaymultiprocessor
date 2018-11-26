<?php

namespace Omnipay\Razorpay\Message;

class Signature
{
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getSignature(array $data)
    {
        $validFields = array_filter(array_keys($data), function ($key) {
            return $key != 'x_signature' and substr($key, 0, 2) == 'x_';
        });

        $data = array_intersect_key($data, array_flip($validFields));

        //sort the array
        ksort($data);

        // prepare the message
        $message = '';

        foreach ($data as $key => $value) {
            $message .= $key.$value;
        }

        return hash_hmac('sha256', $message, $this->key);
    }
}
