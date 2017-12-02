<?php
namespace Omnipay\Pergo;

use Omnipay\Common\AbstractGateway;

/**
 * Gateway class.
 */
class OffsiteGateway extends AbstractGateway
{
    /**
     * @var string
     */
    protected $authenticationToken;
    /**
     * @var int
     */
    protected $billerAccountId;

    /**
     * @var int
     */
    protected $merchantProfileId;

    public function getBillerAccountId()
    {
        return $this->getParameter('billeraccountid');
    }

    public function setBillerAccountId($value)
    {
        return $this->setParameter('billeraccountid', $value);
    }

    public function getAuthenticationToken()
    {
        return $this->getParameter('authenticationtoken');
    }

    public function setAuthenticationToken($value)
    {
        return $this->setParameter('authenticationtoken', $value);
    }

    /**
     * @return string
     */
    public function getMerchantProfileId() {
        return $this->getParameter('merchantProfileId');
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMerchantProfileId($value) {
        return $this->setParameter('merchantProfileId', $value);
    }

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
          'merchantProfileId' => '',
          'testMode' => false,
        );
    }

    /**
     * Authorize credentials.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Pergo\Message\OffsiteAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pergo\Message\OffsiteAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Pergo\Message\OffsiteCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pergo\Message\OffsiteCaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     *
     * @return \Omnipay\Pergo\Message\OffsitePurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pergo\Message\OffsitePurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Pergo\Message\OffsiteCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pergo\Message\OffsiteCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Pergo\Message\CompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pergo\Message\OffsiteCompleteAuthorizeRequest', $parameters);
    }

}
