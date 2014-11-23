<?php

namespace Omnipay\Gopay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Gopay\Api\GopaySoap;

class PurchaseRequest extends AbstractGopayRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @throws InvalidRequestException if proper parameters are not set
     * @return mixed
     */
    public function getData()
    {
        $goId = $this->getGoId();
        if (!is_numeric($goId)) {
            throw new InvalidRequestException("goId should be set to a numeric value, was: " . $goId);
        }

        $secureKey = $this->getSecureKey();
        if (!is_string($secureKey)) {
            throw new InvalidRequestException("secureKey should be set to a string value, was: " . $secureKey);
        }

        return GopaySoap::createPaymentCommand(
            $goId,
            $this->getDescription(),
            $this->getAmountInteger(),
            $this->getCurrency(),
            $this->getTransactionId(),
            $this->getReturnUrl(),
            $this->getCancelUrl(),
            false,
            false,
            null,
            null,
            null,
            null,
            '',
            $secureKey,
            $this->getCard()->getFirstName(),
            $this->getCard()->getLastName(),
            $this->getCard()->getBillingCity(),
            $this->getCard()->getBillingAddress1(),
            $this->getCard()->getBillingPostcode(),
            null, // TODO $this->getCard()->getBillingCountry() returns user input, we need three-letter country code
            $this->getCard()->getEmail(),
            $this->getCard()->getPhone(),
            null, null, null, null, null
        );
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $paymentStatus = $this->soapClient->createPayment($data);
        return $this->response = new PaymentStatusResponse($this, $paymentStatus);
    }
}
