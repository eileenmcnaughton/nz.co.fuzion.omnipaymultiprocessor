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
    /**
     * URl to connect with.
     *
     * Test  - https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Get end point.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set end point.
     *
     * @param string $endpoint
     *   Set URL to redirect to.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function __construct(RequestInterface $request, $data, $end_point)
    {
        $this->request = $request;
        $this->data = $data;
        $this->setEndpoint($end_point);
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
        return false;
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
