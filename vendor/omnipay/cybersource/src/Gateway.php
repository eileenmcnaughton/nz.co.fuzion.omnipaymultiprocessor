<?php
namespace Omnipay\Cybersource;

use Omnipay\Common\AbstractGateway;

/**
 * CyberSource Secure Acceptance Silent Order POST Gateway
 *
 * @link http://apps.cybersource.com/library/documentation/dev_guides/Secure_Acceptance_SOP/html/wwhelp/wwhimpl/js/html/wwhelp.htm
 */
class Gateway extends AbstractGateway
{
  public function getName()
  {
      return 'Cybersource';
  }

/*

use Omnipay\Cybersource\Message\PurchaseRequest;
use Omnipay\Cybersource\Message\RefundRequest;




  public function getDefaultParameters()
  {
    return array(
      'apiKey' => '',
    );
  }

  public function getApiKey()
  {
    return $this->getParameter('apiKey');
  }

  public function setApiKey($value)
  {
    return $this->setParameter('apiKey', $value);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\AuthorizeRequest
   */
  public function authorize(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\AuthorizeRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\CaptureRequest
   */
  public function capture(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\CaptureRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\PurchaseRequest
   */
  public function purchase(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\PurchaseRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\RefundRequest
   */
  public function refund(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\RefundRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\FetchTransactionRequest
   */
  public function fetchTransaction(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\FetchTransactionRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\CreateCardRequest
   */
  public function createCard(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\CreateCardRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\UpdateCardRequest
   */
  public function updateCard(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\UpdateCardRequest', $parameters);
  }

  /**
   * @param array $parameters
   * @return \Omnipay\Cybersource\Message\DeleteCardRequest
   */
  public function deleteCard(array $parameters = array())
  {
    return $this->createRequest('\Omnipay\Cybersource\Message\DeleteCardRequest', $parameters);
  }
}
