<?php

namespace Omnipay\Gopay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Gopay\Api\GopayConfig;
use Omnipay\Gopay\Api\GopayHelper;

class PaymentStatusResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $requestParams = $request->getParameters();
        $secureKey = $requestParams['secureKey'];

        $hashedSignature = GopayHelper::hash(GopayHelper::concatPaymentStatus($data, $secureKey));
        $decryptedHash = GopayHelper::decrypt($data->encryptedSignature, $secureKey);

        if ($decryptedHash != $hashedSignature) {
            throw new InvalidResponseException("Invalid response signature");
        }
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        $data = $this->getData();
        return $data->result == GopayHelper::CALL_COMPLETED
            && $data->sessionState == GopayHelper::PAID;
    }

    public function isTransparentRedirect()
    {
        return false;
    }

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $data = $this->getData();
        return $data->result == GopayHelper::CALL_COMPLETED
            && $data->sessionState == GopayHelper::CREATED
            && $data->paymentSessionId > 0;
    }

    public function getMessage()
    {
        return $this->getData()->resultDescription;
    }

    public function getTransactionReference()
    {
        return $this->getData()->paymentSessionId;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $url = $this->getRequest()->getTestMode() ? GopayConfig::TEST_FULL_URL : GopayConfig::PROD_FULL_URL;

        /** @noinspection PhpUndefinedMethodInspection */
        $goId = $this->getRequest()->getGoId();
        $paymentSessionId = $this->getData()->paymentSessionId;

        /** @noinspection PhpUndefinedMethodInspection */
        $secureKey = $this->getRequest()->getSecureKey();

        return $url . '?' . http_build_query(array(
            'sessionInfo.targetGoId' => $goId,
            'sessionInfo.paymentSessionId' => $paymentSessionId,
            'sessionInfo.encryptedSignature' => GopayHelper::getPaymentSessionSignature(
                $goId, $paymentSessionId, $secureKey)));
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return null;
    }
}
