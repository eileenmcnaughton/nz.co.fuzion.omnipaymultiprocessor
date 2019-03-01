<?php
namespace Omnipay\NMI;

/**
 * NMI Three Step Redirect Gateway
 *
 * @link https://www.nmi.com/
 * @link https://gateway.perpetualpayments.com/merchants/resources/integration/integration_portal.php
 */
class ThreeStepRedirectGateway extends DirectPostGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'NMI Three Step Redirect';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'api_key'      => '',
            'redirect_url' => '',
            'endpoint'     => '',
        );
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    /**
     * @param string
     * @return \Omnipay\Common\AbstractGateway
     */
    public function setApiKey($value)
    {
        return $this->setParameter('api_key', $value);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getParameter('redirect_url');
    }

    /**
     * @param string
     * @return \Omnipay\Common\AbstractGateway
     */
    public function setRedirectUrl($value)
    {
        return $this->setParameter('redirect_url', $value);
    }

    /**
     * Transaction sales are submitted and immediately flagged for settlement.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectSaleRequest
     */
    public function sale(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectSaleRequest', $parameters);
    }

    /**
     * Transaction authorizations are authorized immediately but are not flagged
     * for settlement. These transactions must be flagged for settlement using
     * the capture transaction type. Authorizations typically remain active for
     * three to seven business days.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectAuthRequest
     */
    public function auth(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectAuthRequest', $parameters);
    }

    /**
     * Transaction captures flag existing authorizations for settlement.
     * Only authorizations can be captured. Captures can be submitted for an
     * amount equal to or less than the original authorization.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectCaptureRequest', $parameters);
    }

    /**
     * Transaction voids will cancel an existing sale or captured authorization.
     * In addition, non-captured authorizations can be voided to prevent any
     * future capture. Voids can only occur if the transaction has not been settled.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectVoidRequest', $parameters);
    }

    /**
     * Transaction refunds will reverse a previously settled transaction. If the
     * transaction has not been settled, it must be voided instead of refunded.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectRefundRequest', $parameters);
    }

    /**
     * Transaction credits apply an amount to the cardholder's card that was not
     * originally processed through the Gateway. In most situations credits are
     * disabled as transaction refunds should be used instead.
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectCreditRequest
     */
    public function credit(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectCreditRequest', $parameters);
    }

    /**
     * @param array
     * @return \Omnipay\NMI\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectCreateCardRequest', $parameters);
    }

    /**
     * @param array
     * @return \Omnipay\NMI\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectUpdateCardRequest', $parameters);
    }

    /**
     * @param array
     * @return \Omnipay\NMI\Message\DeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectDeleteCardRequest', $parameters);
    }

    /**
     * @param array
     * @return \Omnipay\NMI\Message\ThreeStepRedirectCompleteActionRequest
     */
    public function completeAction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NMI\Message\ThreeStepRedirectCompleteActionRequest', $parameters);
    }
}
