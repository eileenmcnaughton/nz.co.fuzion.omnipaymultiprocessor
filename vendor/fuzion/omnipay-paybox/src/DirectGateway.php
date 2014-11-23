<?php
namespace Omnipay\Paybox;

use Omnipay\Common\AbstractGateway;

/**
 * Paybox System Gateway
 *
 * @link http://www1.paybox.com/wp-content/uploads/2014/06/ManuelIntegrationPayboxDirect_V6.3_EN.pdf
 * @link http://www1.paybox.com/wp-content/uploads/2014/02/PayboxTestParameters_V6.2_EN.pdf
 */
class DirectGateway extends AbstractGateway
{

    public function getName()
    {
        return 'PayboxSystem';
    }

    public function getDefaultParameters()
    {
        return array(
            'site' => '',
            'rang' => '',
            'identifiant' => '',
            'testMode' => false,
        );
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\DirectAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\DirectCaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\DirectPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\DirectPurchaseRequest', $parameters);
    }

    public function getSite()
    {
        return $this->getParameter('site');
    }

    public function setSite($value)
    {
        return $this->setParameter('site', $value);
    }

    public function getRang()
    {
        return $this->getParameter('rang');
    }

    public function setRang($value)
    {
        return $this->setParameter('rang', $value);
    }

    public function getIdentifiant()
    {
        return $this->getParameter('identifiant');
    }

    public function setIdentifiant($value)
    {
        return $this->setParameter('identifiant', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }
}
