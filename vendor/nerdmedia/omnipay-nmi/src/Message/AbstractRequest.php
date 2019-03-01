<?php
namespace Omnipay\NMI\Message;

/**
* NMI Abstract Request
*/
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $endpoint = 'https://secure.nmi.com/api/transact.php';

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getProcessorId()
    {
        return $this->getParameter('processor_id');
    }

    public function setProcessorId($value)
    {
        return $this->setParameter('processor_id', $value);
    }

    public function getAuthorizationCode()
    {
        return $this->getParameter('authorization_code');
    }

    public function setAuthorizationCode($value)
    {
        return $this->setParameter('authorization_code', $value);
    }

    public function getDescriptor()
    {
        return $this->getParameter('descriptor');
    }

    public function setDescriptor($value)
    {
        return $this->setParameter('descriptor', $value);
    }

    public function getDescriptorPhone()
    {
        return $this->getParameter('descriptor_phone');
    }

    public function setDescriptorPhone($value)
    {
        return $this->setParameter('descriptor_phone', $value);
    }

    public function getDescriptorAddress()
    {
        return $this->getParameter('descriptor_address');
    }

    public function setDescriptorAddress($value)
    {
        return $this->setParameter('descriptor_address', $value);
    }

    public function getDescriptorCity()
    {
        return $this->getParameter('descriptor_city');
    }

    public function setDescriptorCity($value)
    {
        return $this->setParameter('descriptor_city', $value);
    }

    public function getDescriptorState()
    {
        return $this->getParameter('descriptor_state');
    }

    public function setDescriptorState($value)
    {
        return $this->setParameter('descriptor_state', $value);
    }

    public function getDescriptorPostal()
    {
        return $this->getParameter('descriptor_postal');
    }

    public function setDescriptorPostal($value)
    {
        return $this->setParameter('descriptor_postal', $value);
    }

    public function getDescriptorCountry()
    {
        return $this->getParameter('descriptor_country');
    }

    public function setDescriptorCountry($value)
    {
        return $this->setParameter('descriptor_country', $value);
    }

    public function getDescriptorMcc()
    {
        return $this->getParameter('descriptor_mcc');
    }

    public function setDescriptorMcc($value)
    {
        return $this->setParameter('descriptor_mcc', $value);
    }

    public function getDescriptorMerchantId()
    {
        return $this->getParameter('descriptor_merchant_id');
    }

    public function setDescriptorMerchantId($value)
    {
        return $this->setParameter('descriptor_merchant_id', $value);
    }

    public function getDescriptorUrl()
    {
        return $this->getParameter('descriptor_url');
    }

    public function setDescriptorUrl($value)
    {
        return $this->setParameter('descriptor_url', $value);
    }

    public function getOrderId()
    {
        return $this->getParameter('orderid');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderid', $value);
    }

    public function getOrderDescription()
    {
        return $this->getParameter('orderdescription');
    }

    public function setOrderDescription($value)
    {
        return $this->setParameter('orderdescription', $value);
    }

    public function getTax()
    {
        return $this->getParameter('tax');
    }

    public function setTax($value)
    {
        return $this->setParameter('tax', $value);
    }

    public function getShipping()
    {
        return $this->getParameter('shipping');
    }

    public function setShipping($value)
    {
        return $this->setParameter('shipping', $value);
    }

    public function getPONumber()
    {
        return $this->getParameter('ponumber');
    }

    public function setPONumber($value)
    {
        return $this->setParameter('ponumber', $value);
    }

    protected function getBaseData()
    {
        $data = array();

        if (isset($this->type)) {
            $data['type'] = $this->type;
        }

        if (isset($this->customer_vault)) {
            $data['customer_vault'] = $this->customer_vault;
        }

        $data['username'] = $this->getUsername();
        $data['password'] = $this->getPassword();

        if ($this->getProcessorId()) {
            $data['processor_id'] = $this->getProcessorId();
        }

        if ($this->getAuthorizationCode()) {
            $data['authorization_code'] = $this->getAuthorizationCode();
        }

        if ($this->getDescriptor()) {
            $data['descriptor'] = $this->getDescriptor();
        }

        if ($this->getDescriptorPhone()) {
            $data['descriptor_phone'] = $this->getDescriptorPhone();
        }

        if ($this->getDescriptorAddress()) {
            $data['descriptor_address'] = $this->getDescriptorAddress();
        }

        if ($this->getDescriptorCity()) {
            $data['descriptor_city'] = $this->getDescriptorCity();
        }

        if ($this->getDescriptorState()) {
            $data['descriptor_state'] = $this->getDescriptorState();
        }

        if ($this->getDescriptorPostal()) {
            $data['descriptor_postal'] = $this->getDescriptorPostal();
        }

        if ($this->getDescriptorCountry()) {
            $data['descriptor_country'] = $this->getDescriptorCountry();
        }

        if ($this->getDescriptorMcc()) {
            $data['descriptor_mcc'] = $this->getDescriptorMcc();
        }

        if ($this->getDescriptorMerchantId()) {
            $data['descriptor_merchant_id'] = $this->getDescriptorMerchantId();
        }

        if ($this->getDescriptorUrl()) {
            $data['descriptor_url'] = $this->getDescriptorUrl();
        }

        return $data;
    }

    protected function getOrderData()
    {
        $data = array();

        $data['orderid'] = $this->getOrderId();
        $data['orderdescription'] = $this->getOrderDescription();
        $data['tax'] = $this->getTax();
        $data['shipping'] = $this->getShipping();
        $data['ponumber'] = $this->getPONumber();
        $data['ipaddress'] = $this->getClientIp();
        if ($this->getCurrency()) {
            $data['currency'] = $this->getCurrency();
        }

        return $data;
    }

    protected function getBillingData()
    {
        $data = array();

        if ($card = $this->getCard()) {
            $data['firstname'] = $card->getBillingFirstName();
            $data['lastname'] = $card->getBillingLastName();
            $data['company'] = $card->getBillingCompany();
            $data['address1'] = $card->getBillingAddress1();
            $data['address2'] = $card->getBillingAddress2();
            $data['city'] = $card->getBillingCity();
            $data['state'] = $card->getBillingState();
            $data['zip'] = $card->getBillingPostcode();
            $data['country'] = $card->getBillingCountry();
            $data['phone'] = $card->getBillingPhone();
            $data['fax'] = $card->getBillingFax();
            $data['email'] = $card->getEmail();
            // $data['website'] = $card->getWebsite();
        }

        return $data;
    }

    protected function getShippingData()
    {
        $data = array();

        if ($card = $this->getCard()) {
            $data['shipping_firstname'] = $card->getShippingFirstName();
            $data['shipping_lastname'] = $card->getShippingLastName();
            $data['shipping_company'] = $card->getShippingCompany();
            $data['shipping_address1'] = $card->getShippingAddress1();
            $data['shipping_address2'] = $card->getShippingAddress2();
            $data['shipping_city'] = $card->getShippingCity();
            $data['shipping_state'] = $card->getShippingState();
            $data['shipping_zip'] = $card->getShippingPostcode();
            $data['shipping_country'] = $card->getShippingCountry();
            $data['shipping_email'] = $card->getEmail();
        }

        return $data;
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            http_build_query($data, '', '&')
        );

        return $this->response = new DirectPostResponse($this, $httpResponse->getBody()->getContents());
    }

    public function setEndpoint($value)
    {
        return $this->setParameter('endpoint', $value);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
