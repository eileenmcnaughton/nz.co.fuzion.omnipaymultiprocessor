<?php
namespace Omnipay\Paybox;

use Omnipay\Common\AbstractGateway;

/**
 * Paybox System Gateway
 *
 * @link http://www1.paybox.com/wp-content/uploads/2014/06/ManuelIntegrationPayboxDirect_V6.3_EN.pdf
 * @link http://www1.paybox.com/wp-content/uploads/2014/02/PayboxTestParameters_V6.2_EN.pdf
 */
class SystemGateway extends AbstractGateway
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
        return $this->createRequest('\Omnipay\Paybox\Message\AuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\CaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\PurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\CompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\CompleteAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\CreateCardRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Paybox\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paybox\Message\UpdateCardRequest', $parameters);
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

    public function generateSignature($data)
    {
      /** @var copied from paybox $binKey */
      $binKey = pack('H*', $this->globals['hmac_key']);
      return hash_hmac($this->globals['hmac_algorithm'], $this->stringifyParameters(), $binKey);
      //from cybersource
        $data_to_sign = array();
        foreach ($data as $key => $value)
        {
            $data_to_sign[] = $key . "=" . $value;
        }
        $pairs = implode(',', $data_to_sign);

        return base64_encode(hash_hmac('sha256', $pairs, $this->getSecretKey(), true));
    }
}
