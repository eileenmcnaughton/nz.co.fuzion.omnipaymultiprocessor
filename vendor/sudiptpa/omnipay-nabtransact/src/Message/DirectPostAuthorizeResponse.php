<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * NABTransact Direct Post Authorize Response.
 */
class DirectPostAuthorizeResponse extends AbstractResponse implements RedirectResponseInterface
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
        $this->request = $request;
        $this->data = $data;
        $this->redirectUrl = $redirectUrl;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return array
     */
    public function getRedirectData()
    {
        return $this->getData();
    }
}
