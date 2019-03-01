<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Authorize Request
 */
class ThreeStepRedirectAuthRequest extends ThreeStepRedirectAbstractRequest
{
    /**
     * @var string
     */
    protected $type = 'auth';

    /**
     * Override Duplicate Transaction Detection checking (in seconds).
     *
     * @param boolean
     * @return AbstractRequest Provides a fluent interface
     */
    public function setDupSeconds($value)
    {
        return $this->setParameter('dup_seconds', $value);
    }

    /**
     * @return string
     */
    public function getDupSeconds()
    {
        return $this->getParameter('dup_seconds');
    }

    /**
     * Sets the add customer.
     *
     * @param boolean
     * @return AbstractRequest Provides a fluent interface
     */
    public function setAddCustomer($value)
    {
        return $this->setParameter('add_customer', $value);
    }

    /**
     * @return string
     */
    public function getAddCustomer()
    {
        return $this->getParameter('add_customer');
    }

    /**
     * Sets the update customer.
     *
     * @param string
     * @return AbstractRequest Provides a fluent interface
     */
    public function setUpdateCustomer($value)
    {
        return $this->setParameter('update_customer', $value);
    }

    /**
     * @return string
     */
    public function getUpdateCustomer()
    {
        return $this->getParameter('update_customer');
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('amount');

        $data = array(
            'api-key'      => $this->getApiKey(),
            'redirect-url' => $this->getRedirectUrl(),
            'amount'       => $this->getAmount(),
        );

        if ($this->getDupSeconds()) {
            $data['dup-seconds'] = $this->getDupSeconds();
        }

        if ($this->getMerchantDefinedField1()) {
            $data['merchant-defined-field-1'] = $this->getMerchantDefinedField1();
        }

        if ($this->getMerchantDefinedField2()) {
            $data['merchant-defined-field-2'] = $this->getMerchantDefinedField2();
        }

        if ($this->getMerchantDefinedField3()) {
            $data['merchant-defined-field-3'] = $this->getMerchantDefinedField3();
        }

        if ($this->getMerchantDefinedField4()) {
            $data['merchant-defined-field-4'] = $this->getMerchantDefinedField4();
        }

        if ($this->getCardReference()) {
            $data['customer-vault-id'] = $this->getCardReference();
        }
        else {
            $data = array_merge(
                $data,
                $this->getOrderData(),
                $this->getBillingData(),
                $this->getShippingData()
            );

            if ($this->getAddCustomer()) {
                $data['add-customer'] = [];
            }

            if ($this->getUpdateCustomer()) {
                $data['update-customer'] = [
                    'customer-vault-id' => $this->getUpdateCustomer(),
                ];
            }
        }

        return $data;
    }
}
