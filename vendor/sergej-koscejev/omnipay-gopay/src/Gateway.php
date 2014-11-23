<?php

namespace Omnipay\Gopay;

use Guzzle\Http\ClientInterface;
use Omnipay\Common\AbstractGateway;
use Omnipay\Gopay\Api\GopayConfig;
use Omnipay\Gopay\Api\GopaySoap;
use Symfony\Component\HttpFoundation\Request;

class Gateway extends AbstractGateway {

    /**
     * @var \SoapClient
     */
    private $soapClient;

    public function __construct($soapClient = null, ClientInterface $httpClient = null, Request $httpRequest = null)
    {
        $this->soapClient = $soapClient;
        parent::__construct($httpClient, $httpRequest);
    }

    public function getName()
    {
        return 'GoPay';
    }

    public function getDefaultParameters()
    {
        return parent::getDefaultParameters() + array('testMode' => false, 'goId' => '', 'secureKey' => '');
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

    protected function createRequest($class, array $parameters)
    {
        $obj = new $class($this->getSoapClient(), $this->httpClient, $this->httpRequest);
        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }

    public function getSoapClient()
    {
        if (is_null($this->soapClient))
        {
            $url = $this->getTestMode() ? GopayConfig::TEST_WSDL_URL : GopayConfig::PROD_WSDL_URL;
            $this->soapClient = GopaySoap::createSoapClient($url, $this->getTestMode() ? array('trace' => true) : array());
        }

        return $this->soapClient;
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Gopay\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Gopay\Message\CompletePurchaseRequest', $parameters);
    }
}
