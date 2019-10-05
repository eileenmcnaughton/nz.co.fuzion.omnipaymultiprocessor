<?php namespace Omnipay\Beanstream;

use Omnipay\Common\AbstractGateway;

/**
 * Beanstream Gateway
 *
 * @link https://www.beanstream.com/
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Beanstream';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'apiPasscode' => '',
            'username' => '',
            'password' => ''
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getApiPasscode()
    {
        return $this->getParameter('apiPasscode');
    }

    public function setApiPasscode($value)
    {
        return $this->setParameter('apiPasscode', $value);
    }

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

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\AuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Beanstream\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\RefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Beanstream\Message\VoidReqeust
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\VoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Beanstream\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\CaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\CreateProfileRequest
     */
    public function createProfile(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\CreateProfileRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\FetchProfileRequest
     */
    public function fetchProfile(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\FetchProfileRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\UpdateProfileRequest
     */
    public function updateProfile(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\UpdateProfileRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\DeleteProfileRequest
     */
    public function deleteProfile(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\DeleteProfileRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\CreateProfileCardRequest
     */
    public function createProfileCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\CreateProfileCardRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\FetchProfileCardsRequest
     */
    public function fetchProfileCards(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\FetchProfileCardsRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\UpdateProfileCardsRequest
     */
    public function updateProfileCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\UpdateProfileCardRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Beanstream\Message\DeleteProfileCardRequest
     */
    public function deleteProfileCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\DeleteProfileCardRequest', $parameters);
    }
}
