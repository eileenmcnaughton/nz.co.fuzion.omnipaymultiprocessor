<?php

namespace Omnipay\NMI\Message;

use Omnipay\Common\CreditCard;
use RuntimeException;
use SimpleXMLElement;

/**
 * NMI Three Step Redirect Abstract Request
 */
abstract class ThreeStepRedirectAbstractRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://secure.nmi.com/api/v2/three-step';

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    /**
     * @param string
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setApiKey($value)
    {
        return $this->setParameter('api_key', $value);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getParameter('redirect_url');
    }

    /**
     * @param string
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setRedirectUrl($value)
    {
        return $this->setParameter('redirect_url', $value);
    }

    /**
     * @return string
     */
    public function getTokenId()
    {
        return $this->getParameter('token_id');
    }

    /**
     * @param string
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTokenId($value)
    {
        return $this->setParameter('token_id', $value);
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCard($value)
    {
        if (!$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
    }

    /**
     * @return string
     */
    public function getSecCode()
    {
        return $this->getParameter('sec_code');
    }

    /**
     * @param string
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSecCode($value)
    {
        return $this->setParameter('sec_code', $value);
    }

    /**
     * @return string
     */
    public function getMerchantDefinedField1()
    {
        return $this->getParameter('merchant_defined_field_1');
    }

    /**
     * Sets the first merchant defined field.
     *
     * @param string
     * @return AbstractRequest Provides a fluent interface
     */
    public function setMerchantDefinedField1($value)
    {
        return $this->setParameter('merchant_defined_field_1', $value);
    }

    /**
     * @return string
     */
    public function getMerchantDefinedField2()
    {
        return $this->getParameter('merchant_defined_field_2');
    }

    /**
     * Sets the second merchant defined field.
     *
     * @param string
     * @return AbstractRequest Provides a fluent interface
     */
    public function setMerchantDefinedField2($value)
    {
        return $this->setParameter('merchant_defined_field_2', $value);
    }

    /**
     * @return string
     */
    public function getMerchantDefinedField3()
    {
        return $this->getParameter('merchant_defined_field_3');
    }

    /**
     * Sets the third merchant defined field.
     *
     * @param string
     * @return AbstractRequest Provides a fluent interface
     */
    public function setMerchantDefinedField3($value)
    {
        return $this->setParameter('merchant_defined_field_3', $value);
    }

    /**
     * @return string
     */
    public function getMerchantDefinedField4()
    {
        return $this->getParameter('merchant_defined_field_4');
    }

    /**
     * Sets the fourth merchant defined field.
     *
     * @param string
     * @return AbstractRequest Provides a fluent interface
     */
    public function setMerchantDefinedField4($value)
    {
        return $this->setParameter('merchant_defined_field_4', $value);
    }

    /**
     * @return array
     */
    protected function getOrderData()
    {
        $data = array();

        $data['order-id'] = $this->getOrderId();
        $data['order-description'] = $this->getOrderDescription();
        $data['tax-amount'] = $this->getTax();
        $data['shipping-amount'] = $this->getShipping();
        $data['po-number'] = $this->getPONumber();
        $data['ip-address'] = $this->getClientIp();

        if ($this->getCurrency()) {
            $data['currency'] = $this->getCurrency();
        }

        if ($this->getSecCode()) {
            $data['sec-code'] = $this->getSecCode();
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getBillingData()
    {
        $data = array();

        if ($card = $this->getCard()) {
            $data['billing'] = array(
                'first-name' => $card->getBillingFirstName(),
                'last-name'  => $card->getBillingLastName(),
                'address1'   => $card->getBillingAddress1(),
                'city'       => $card->getBillingCity(),
                'state'      => $card->getBillingState(),
                'postal'     => $card->getBillingPostcode(),
                'country'    => $card->getBillingCountry(),
                'phone'      => $card->getBillingPhone(),
                'email'      => $card->getEmail(),
                'company'    => $card->getBillingCompany(),
                'address2'   => $card->getBillingAddress2(),
                'fax'        => $card->getBillingFax(),
            );
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getShippingData()
    {
        $data = array();

        if ($card = $this->getCard()) {
            $data['shipping'] = array(
                'first-name' => $card->getShippingFirstName(),
                'last-name'  => $card->getShippingLastName(),
                'address1'   => $card->getShippingAddress1(),
                'city'       => $card->getShippingCity(),
                'state'      => $card->getShippingState(),
                'postal'     => $card->getShippingPostcode(),
                'country'    => $card->getShippingCountry(),
                'email'      => $card->getEmail(),
                'company'    => $card->getShippingCompany(),
                'address2'   => $card->getShippingAddress2(),
            );
        }

        return $data;
    }

    /**
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectResponse
     */
    public function sendData($data)
    {
        $document = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><'.$this->type.'/>');
        $this->arrayToXml($document, $data);

        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array(
                'Content-Type' => 'text/xml',
                'User-Agent'   => 'Omnipay',
            ),
            $document->asXML()
        );

        $xml = static::xmlDecode($httpResponse);

        return $this->response = new ThreeStepRedirectResponse($this, $xml);
    }

    /**
     * Parse the XML response body and return a \SimpleXMLElement.
     *
     * In order to prevent XXE attacks, this method disables loading external
     * entities. If you rely on external entities, then you must parse the
     * XML response manually by accessing the response body directly.
     *
     * Copied from Response->xml() in Guzzle3 (copyright @mtdowling)
     * @link https://github.com/guzzle/guzzle3/blob/v3.9.3/src/Guzzle/Http/Message/Response.php
     *
     * @param  string|ResponseInterface $response
     * @return \SimpleXMLElement
     * @throws RuntimeException if the response body is not in XML format
     * @link http://websec.io/2012/08/27/Preventing-XXE-in-PHP.html
     *
     */
    public static function xmlDecode($response)
    {
        if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $body = $response->getBody()->__toString();
        } else {
            $body = (string) $response;
        }

        $errorMessage = null;
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
        libxml_clear_errors();

        try {
            $xml = new \SimpleXMLElement((string) $body ?: '<root />', LIBXML_NONET);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);

        if ($errorMessage !== null) {
            throw new \InvalidArgumentException('SimpleXML error: ' . $errorMessage);
        }

        return $xml;
    }

    /**
     * @param \SimpleXMLElement
     * @param array
     * @return void
     */
    private function arrayToXml(SimpleXMLElement $parent, array $data)
    {
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $child = $parent->addChild($name);
                $this->arrayToXml($child, $value);
            }
            else {
                $parent->addChild($name, htmlspecialchars($value));
            }
        }
    }
}
