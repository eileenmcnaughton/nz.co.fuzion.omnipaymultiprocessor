<?php

namespace Omnipay\Paypalstandard\Message;

use Omnipay\Paypalstandard\Message\AbstractRequest;

/**
 * Paypal Standard Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{

    public function getData()
    {
        foreach ($this->getRequiredCoreFields() as $field) {
            $this->validate($field);
        }
        $this->validateCardFields();
        $data = $this->getBaseData() + $this->getTransactionData() + $this->getURLData();
        return $data;
    }


    /**
     * sendData function. In this case, where the browser is to be directly it constructs and returns a response object
     * @param mixed $data
     * @return \Omnipay\Common\Message\ResponseInterface|AuthorizeResponse
     */
    public function sendData($data)
    {
        return $this->response = new AuthorizeResponse($this, $data, $this->getEndpoint());
    }

    protected function createResponse($data)
    {
        return $this->response = new AuthorizeResponse($this, $data, $this->getEndpoint());
    }

    public function getSite()
    {
        return $this->getParameter('site');
    }

    public function setSite($value)
    {
        return $this->setParameter('site', $value);
    }

    public function getMerchantAccountEmail()
    {
        return $this->getParameter('MerchantAccountEmail');
    }

    public function setMerchantAccountEmail($value)
    {
        return $this->setParameter('MerchantAccountEmail', $value);
    }

    /**
     * Get an array of the required fields for the core gateway
     * @return array
     */
    public function getRequiredCoreFields()
    {
        return array
        (
            'amount',
            'currency',
        );
    }

    /**
     * get an array of the required 'card' fields (personal information fields)
     * @return array
     */
    public function getRequiredCardFields()
    {
        return array
        (
            'email',
        );
    }

    /**
     * Get values for IPN and browser return urls.
     *
     * Browser return urls should all be set or non set.
     *
     * https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */
    public function getURLData()
    {
        $data = array();
        $data['ipn_notification_url'] = urlencode($this->getNotifyUrl());
        $data['return_url'] = $this->getReturnUrl();
        $data['cancel_return'] = $this->getCancelUrl();
        return $data;
    }

    /**
     * @return string
     */
    public function getUniqueID()
    {
        return uniqid();
    }

    /**
     * Map Omnipay normalised fields to gateway defined fields. If the order the fields are
     * passed to the gateway matters you should order them correctly here
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */

    /**
     * https://www.paypal.com/cgi-bin/webscr?business=adc%40lllc.ca
     * &notify_url=http%3A//master.localhost/sites/all/modules/civicrm/extern/ipn.php%3Freset%3D1%26contactID%3D202%26contributionID%3D144%26module%3Dcontribute
     * &item_name=Online+Contribution%3A+Help+Support+CiviCRM%21
     * &quantity=1
     * &undefined_quantity=0
     * &cancel_return=http%3A//master.localhost/civicrm/contribute/transact%3F_qf_Main_display%3D1%26cancel%3D1%26qfKey%3D625fc646d33436867a2bd9ff00a2fa91_5405
     * &no_note=1
     * &no_shipping=1
     * &return=http%3A//master.localhost/civicrm/contribute/transact%3F_qf_ThankYou_display%3D1%26qfKey%3D625fc646d33436867a2bd9ff00a2fa91_5405
     * &rm=2
     * &currency_code=CAD
     * &invoice=bda021136b22877a700635194ebeac8c
     * &lc=US
     * &charset=UTF-8
     * &bn=CiviCRM_SP
     * &email=demo%40example.com
     * &cmd=_xclick
     * &amount=1
     */
    public function getTransactionData()
    {
        return array
        (
            'amount' => $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'email' => $this->getCard()->getEmail(),
            'transaction_id' => $this->getTransactionId(),
            'item_name' => $this->getDescription(),
        );
    }

    /**
     * @return array
     * Get data that is common to all requests - generally aut
     */

    public function getBaseData()
    {
        return array(
            'business' => $this->getMerchantAccountEmail(),
        );
    }

    /**
     * this is the url provided by your payment processor. Github is standing in for the real url here
     * @return string
     */
    public function getEndpoint()
    {
        return 'https://www.paypal.com/cgi-bin/webscr';
    }

    public function getTransactionType()
    {
        return 'Authorize';
    }

    public function getPaymentMethod()
    {
        // return 'card';
    }

}
