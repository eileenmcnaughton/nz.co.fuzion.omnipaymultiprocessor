<?php
namespace Omnipay\Cybersource;

use Omnipay\Common\AbstractGateway;
use Omnipay\Cybersource\Message\PurchaseRequest;
use Omnipay\Cybersource\Message\RefundRequest;

/**
 * CyberSource Secure Acceptance Silent Order POST Gateway
 *
 * @link http:
 * //apps.cybersource.com/library/documentation/dev_guides/Secure_Acceptance_SOP/html/wwhelp/wwhimpl/js/html/wwhelp.htm
 */
class Gateway extends AbstractGateway
{

    public function getName()
    {
        return 'Cybersource';
    }

    public function getDefaultParameters()
    {
        return array(
            'profileId' => '',
            'secretKey' => '',
            'accessKey' => '',
            'testMode' => false,
        );
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\AuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\CaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\PurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\CompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\CompleteAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\CreateCardRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\UpdateCardRequest', $parameters);
    }

    public function getProfileId()
    {
        return $this->getParameter('profileId');
    }

    public function setProfileId($value)
    {
        return $this->setParameter('profileId', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getAccessKey()
    {
        return $this->getParameter('accessKey');
    }

    public function setAccessKey($value)
    {
        return $this->setParameter('accessKey', $value);
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
        $data_to_sign = array();
        foreach ($data as $key => $value) {
            $data_to_sign[] = $key . "=" . $value;
        }
        $pairs = implode(',', $data_to_sign);

        return base64_encode(hash_hmac('sha256', $pairs, $this->getSecretKey(), true));
    }
}
