<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class SystemResponse extends AbstractResponse implements RedirectResponseInterface
{
    public $endpoint = 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function isTransparentRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->endpoint . '?' . http_build_query($this->data);
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }
}
