<?php

namespace Omnipay\Gopay\Message;

use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\Message\AbstractRequest;
use SoapClient;

/**
 * Abstract Request
 */
abstract class AbstractGopayRequest extends AbstractRequest
{
    /**
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * Create a new Request
     *
     * @param SoapClient $soapClient A SoapClient to make calls with
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(SoapClient $soapClient, ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->soapClient = $soapClient;
        $this->initialize();
    }

    public function getGoId()
    {
        return $this->getParameter('goId');
    }

    public function setGoId($value)
    {
        return $this->setParameter('goId', $value);
    }

    public function getSecureKey()
    {
        return $this->getParameter('secureKey');
    }

    public function setSecureKey($value)
    {
        return $this->setParameter('secureKey', $value);
    }

}
