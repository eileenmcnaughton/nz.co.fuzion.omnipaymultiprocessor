<?php

namespace Omnipay\Razorpay\Message;

class CompletePurchaseResponse extends PurchaseResponse
{
    public function isSuccessful()
    {
        if (!empty($_POST['x_result'])) {
            $data = parent::getRedirectData();

            $hmacKey = $data['key_secret'];
            $razorpaySignature = $_POST['x_signature'];

            $verifySignature = new Signature($hmacKey);
            $signature = $verifySignature->getSignature($_POST);

            return $this->hashEquals($signature, $razorpaySignature);
        }

        return false;
    }

    /*
     * Taken from https://stackoverflow.com/questions/10576827/secure-string-compare-function
     * under the MIT license
     */
    protected function hashEquals($str1, $str2)
    {
        if (function_exists('hash_equals') === true) {
            return hash_equals($str1, $str2);
        }
        if (strlen($str1) !== strlen($str2)) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < strlen($str1); $i++) {
            $result |= ord($str1) ^ ord($str2);
        }

        return ($result === 0);
    }
}
