<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Purchase Request.
 */
class SecureXMLRiskPurchaseRequest extends SecureXMLAbstractRequest
{
    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/riskmgmt/payment';

    /**
     * @var string
     */
    public $testEndpoint = 'https://demo.transact.nab.com.au/riskmgmt/payment';

    /**
     * @var int
     */
    protected $txnType = 0;

    /**
     * @var array
     */
    protected $requiredFields = ['amount', 'card', 'transactionId', 'ip'];

    public function setIp($value)
    {
        $this->setParameter('ip', $value);
    }

    public function getIp()
    {
        return $this->getParameter('ip');
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = $this->getBasePaymentXMLWithCard();

        $buyer = $xml->addChild('BuyerInfo');

        $buyer->addChild('ip', $this->getIp('ip'));

        $card = $this->getCard();

        if ($firstName = $card->getFirstName()) {
            $buyer->addChild('firstName', $firstName);
        }
        if ($lastName = $card->getLastName()) {
            $buyer->addChild('firstName', $lastName);
        }
        if ($postCode = $card->getBillingPostcode()) {
            $buyer->addChild('zipcode', $postCode);
        }
        if ($city = $card->getBillingCity()) {
            $buyer->addChild('town', $city);
        }
        if ($country = $card->getBillingCountry()) {
            $buyer->addChild('billingCountry', $country);
        }
        if ($email = $card->getEmail()) {
            $buyer->addChild('emailAddress', $email);
        }

        return $xml;
    }
}
