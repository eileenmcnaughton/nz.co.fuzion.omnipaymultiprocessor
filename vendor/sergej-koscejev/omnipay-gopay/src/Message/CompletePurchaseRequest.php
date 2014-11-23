<?php


namespace Omnipay\Gopay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Gopay\Api\GopayHelper;

class CompletePurchaseRequest extends AbstractGopayRequest {

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @throws InvalidRequestException on signature mismatch
     * @return mixed
     */
    public function getData()
    {
        $request = $this->httpRequest->query;

        $expectedSignature = GopayHelper::getPaymentIdentitySignature(
            $request->get('targetGoId'), $request->get('paymentSessionId'), $request->get('parentPaymentSessionId'),
            $request->get('orderNumber'), $this->getSecureKey());

        if ($expectedSignature != $request->get('encryptedSignature')) {
            throw new InvalidRequestException("Invalid response signature");
        }

        return $request->all();
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $paymentSession = array(
            "targetGoId" => (float)$this->getGoId(),
            "paymentSessionId" => (float)$data['paymentSessionId'],
            "encryptedSignature" => GopayHelper::getPaymentSessionSignature($this->getGoId(), $data['paymentSessionId'],
                $this->getSecureKey()));

        $paymentStatus = $this->soapClient->paymentStatus($paymentSession);
        return new PaymentStatusResponse($this, $paymentStatus);
    }
}
