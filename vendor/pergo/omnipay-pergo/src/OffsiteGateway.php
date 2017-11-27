<?php
namespace Omnipay\Pergo;

use Omnipay\Common\AbstractGateway;

/**
 * Gateway class.
 */
class OffsiteGateway extends AbstractGateway
{

    /**
     * Get the processor name.
     *
     * @return string
     */
    public function getName()
    {
        return 'pergo_Offsite';
    }

    /**
     * Declare the parameters that will be used to authenticate with the site.
     *
     * A getter (e.g getUsername for username) is required for each of these.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
          'authenticationToken' => '',
          'billerAccountId' => '',
          'testMode' => false,
        );
    }

    /**
     * Authorize credentials.
     *
     * @param array $parameters
     *
     * @return \Omnipay\pergo\Message\OffsiteAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\pergo\Message\OffsiteAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\pergo\Message\OffsiteCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\pergo\Message\OffsiteCaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\pergo\Message\OffsitePurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\pergo\Message\OffsitePurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\pergo\Message\OffsiteCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\pergo\Message\OffsiteCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\pergo\Message\CompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\pergo\Message\OffsiteCompleteAuthorizeRequest', $parameters);
    }

    public function getAuthenticationToken()
    {
        return $this->getParameter('authenticationtoken');
    }

    public function setAuthenticationToken($value)
    {
        return $this->setParameter('authenticationtoken', $value);
    }
    public function getBillerAccountId()
    {
        return $this->getParameter('billeraccountid');
    }

    public function setBillerAccountId($value)
    {
        return $this->setParameter('billeraccountid', $value);
    }
}
