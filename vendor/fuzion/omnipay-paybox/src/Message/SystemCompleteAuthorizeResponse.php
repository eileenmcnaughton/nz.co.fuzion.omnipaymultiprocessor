<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

/**
 * Paybox Complete Authorize Response
 */
class SystemCompleteAuthorizeResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $signed_data_keys = array(
            'Mt',
            'Id',
            'Ref',
            'Erreur',
        );
        foreach ($signed_data_keys as $key) {
            if (isset($data[$key])) {
                $this->data[$key] = $data[$key];
            }
        }

        if (empty($data['sign'])) {
            throw new InvalidResponseException('Unsigned response');
        }
        $this->signature = $data['sign'];

        if (!$this->verifySignature()) {
            throw new InvalidResponseException('Incorrectly signed response');
        }
    }

    public function isSuccessful()
    {
        return isset($this->data['Erreur']) && '00000' === $this->data['Erreur'];
    }

    public function getTransactionReference()
    {
        return isset($this->data['Ref']) ? $this->data['Ref'] : null;
    }

    public function getTransactionId()
    {
        return isset($this->data['Id']) ? $this->data['Id'] : null;
    }

    public function getMessage()
    {
        return !$this->isSuccessful() ? 'Transaction failed' : null;
    }

    /**
     * Get public key file path.
     *
     * @return string
     *  Full path to Paybox public key.
     */
    protected function getPublicKey()
    {
        return  __DIR__ . '/../Resources/paybox_public_key.pem';
    }

    /**
     * Verifies the validity of the signature.
     *
     * Function adapted from LexikPayboxBundle.
     * @return bool
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     * @throws \Omnipay\Paybox\Message\InvalidRequestException
     */
    public function verifySignature()
    {
        $this->initSignature();
        $file = fopen($this->getPublicKey(), 'r');
        $cert = fread($file, 1024);
        fclose($file);
        $publicKey = openssl_pkey_get_public($cert);

        $result = openssl_verify(
            $this->stringify($this->data),
            $this->signature,
            $publicKey,
            'sha1WithRSAEncryption'
        );
        openssl_free_key($publicKey);
        if ($result == 1) {
            return true;
        } elseif ($result == 0) {
            throw new InvalidResponseException('Signature is invalid.');
        } else {
            throw new InvalidResponseException('Error while verifying Signature.');
        }

        return false;
    }

    /**
     * Concatenates all parameters set in the http request.
     *
     * Function adapted from LexikPayboxBundle.
     */
    protected function initData()
    {
        foreach ($this->httpRequest->request->all() as $key => $value) {
            $this->data[$key] = urlencode($value);
        }
    }

    /**
     * Makes an array of parameters become a query-string like string.
     *
     * Function adapted from LexikPayboxBundle.
     *
     * @param array $array
     *
     * @return string
     */
    protected function stringify(array $array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $result[] = sprintf('%s=%s', $key, $value);
        }
        return implode('&', $result);
    }
    /**
     * Gets the signature set in the http request.
     *
     * Function adapted from LexikPayboxBundle.
     *
     * Paybox documentation says :
     * The Paybox signature is created by encrypting a SHA-1 hash with the private Paybox RSA key. The size
     * of a SHA-1 hash is 160 bits and the size of the Paybox key is 1024 bits. The signature is always a binary
     * value of fixed 128 bytes size (172 bytes in Base64 encoding).
     *
     * But sometimes, base64 encoded signature are also url encoded.
     */
    protected function initSignature()
    {
        if (empty($this->signature)) {
            throw new InvalidRequestException('Signature not set');
        }
        $signature = $this->signature;
        $signatureLength = strlen($signature);
        if ($signatureLength > 172) {
            $this->signature = base64_decode(urldecode($signature));
            return true;
        } elseif ($signatureLength == 172) {
            $this->signature = base64_decode($signature);
            return true;
        } elseif ($signatureLength == 128) {
            $this->signature = $signature;
            return true;
        } else {
            $this->signature = null;
            throw new InvalidRequestException('Bad signature format.');
        }
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setIdentifiant($value)
    {
        return $this->setParameter('amount', $value);
    }
}
