<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * NABTransact HostedPayment Purchase Response.
 */
class HostedPaymentPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @param RequestInterface $request
     * @param $data
     * @param $redirectUrl
     */
    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        parent::__construct($request, $data);
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return array
     */
    public function getRedirectData()
    {
        return $this->getData();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function isRedirect()
    {
        return true;
    }

    public function isSuccessful()
    {
        return false;
    }
}
