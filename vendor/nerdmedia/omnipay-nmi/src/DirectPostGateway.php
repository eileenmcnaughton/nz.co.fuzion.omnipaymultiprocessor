<?php
namespace Omnipay\NMI;

use Omnipay\Common\AbstractGateway;

/**
 * NMI Direct Post Gateway
 *
 * @link https://www.nmi.com/
 * @link https://gateway.perpetualpayments.com/merchants/resources/integration/integration_portal.php
 */
class DirectPostGateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'NMI Direct Post';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'password' => ''
        );
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return string
     */
    public function getProcessorId()
    {
        return $this->getParameter('processor_id');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setProcessorId($value)
    {
        return $this->setParameter('processor_id', $value);
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->getParameter('authorization_code');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setAuthorizationCode($value)
    {
        return $this->setParameter('authorization_code', $value);
    }

    /**
     * @return string
     */
    public function getDescriptor()
    {
        return $this->getParameter('descriptor');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptor($value)
    {
        return $this->setParameter('descriptor', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorPhone()
    {
        return $this->getParameter('descriptor_phone');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorPhone($value)
    {
        return $this->setParameter('descriptor_phone', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorAddress()
    {
        return $this->getParameter('descriptor_address');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorAddress($value)
    {
        return $this->setParameter('descriptor_address', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorCity()
    {
        return $this->getParameter('descriptor_city');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorCity($value)
    {
        return $this->setParameter('descriptor_city', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorState()
    {
        return $this->getParameter('descriptor_state');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorState($value)
    {
        return $this->setParameter('descriptor_state', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorPostal()
    {
        return $this->getParameter('descriptor_postal');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorPostal($value)
    {
        return $this->setParameter('descriptor_postal', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorCountry()
    {
        return $this->getParameter('descriptor_country');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorCountry($value)
    {
        return $this->setParameter('descriptor_country', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorMcc()
    {
        return $this->getParameter('descriptor_mcc');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorMcc($value)
    {
        return $this->setParameter('descriptor_mcc', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorMerchantId()
    {
        return $this->getParameter('descriptor_merchant_id');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDescriptorMerchantId($value)
    {
        return $this->setParameter('descriptor_merchant_id', $value);
    }

    /**
     * @return string
     */
    public function getDescriptorUrl()
    {
        return $this->getParameter('descriptor_url');
    }

    public function setDescriptorUrl($value)
    {
        return $this->setParameter('descriptor_url', $value);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setEndpoint($value)
    {
        return $this->setParameter('endpoint', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->sale($parameters);
    }

    public function authorize(array $parameters = array())
    {
        return $this->auth($parameters);
    }

    /**
     * Transaction sales are submitted and immediately flagged for settlement.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostSaleRequest
     */
    public function sale(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostSaleRequest', $parameters);
    }

    /**
     * Transaction authorizations are authorized immediately but are not flagged
     * for settlement. These transactions must be flagged for settlement using
     * the capture transaction type. Authorizations typically remain active for
     * three to seven business days.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostAuthRequest
     */
    public function auth(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostAuthRequest', $parameters);
    }

    /**
     * Transaction captures flag existing authorizations for settlement.
     * Only authorizations can be captured. Captures can be submitted for an
     * amount equal to or less than the original authorization.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostCaptureRequest', $parameters);
    }

    /**
     * Transaction voids will cancel an existing sale or captured authorization.
     * In addition, non-captured authorizations can be voided to prevent any
     * future capture. Voids can only occur if the transaction has not been settled.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostVoidRequest', $parameters);
    }

    /**
     * Transaction refunds will reverse a previously settled transaction. If the
     * transaction has not been settled, it must be voided instead of refunded.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostRefundRequest', $parameters);
    }

    /**
     * Transaction credits apply an amount to the cardholder's card that was not
     * originally processed through the Gateway. In most situations credits are
     * disabled as transaction refunds should be used instead.
     * @param  array  $parameters
     * @return \Omnipay\NMI\Message\DirectPostCreditRequest
     */
    public function credit(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostCreditRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\NMI\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostCreateCardRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\NMI\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostUpdateCardRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\NMI\Message\DeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\DirectPostDeleteCardRequest', $parameters);
    }
}
