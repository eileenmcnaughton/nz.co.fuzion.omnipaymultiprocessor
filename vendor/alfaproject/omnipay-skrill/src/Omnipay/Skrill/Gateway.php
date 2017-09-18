<?php
namespace Omnipay\Skrill;

use Omnipay\Common\AbstractGateway;

/**
 * Skrill Gateway
 *
 * @author    Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.0
 */
class Gateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Skrill';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'email'      => '',
            'notifyUrl'  => '',
            'testMode'   => false,
        ];
    }

    /**
     * Get the merchant's email address.
     *
     * @return string email
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Set the merchant's email address.
     *
     * @param string $value email
     *
     * @return self
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the merchant's MD5 API/MQI password.
     *
     * @return string password
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Set the merchant's MD5 API/MQI password.
     *
     * @param string $value password
     *
     * @return self
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Get the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * @return string notify url
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }

    /**
     * Set the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * Alternatively you may specify an email address to which you would like to receive
     * the results. If the notify url is omitted, no transaction details will be sent to
     * the merchant.
     *
     * @param string $value notify url
     *
     * @return self
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }

    /**
     * Get the secret word used for signatures.
     *
     * @return string Secret word
     */
    public function getSecretWord()
    {
        return $this->getParameter('secretWord');
    }

    /**
     * Set the secret word used for signatures.
     *
     * @param string $value Secret word
     *
     * @return self
     */
    public function setSecretWord($value)
    {
        return $this->setParameter('secretWord', $value);
    }

    /**
     * Create a new charge.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Skrill\Message\PaymentRequest', $parameters);
    }

    /**
     * Finalises a payment (callback).
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Skrill\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * Authorize and prepare a transfer.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\AuthorizeResponse               response
     */
    public function authorizeTransfer(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Skrill\Message\AuthorizeTransferRequest', $parameters);
    }

    /**
     * Create a new transfer.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\TransferResponse               response
     */
    public function transfer(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Skrill\Message\TransferRequest', $parameters);
    }

    /**
     * Authorize and prepare a refund.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\AuthorizeResponse               response
     */
    public function authorizeRefund(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Skrill\Message\AuthorizeRefundRequest', $parameters);
    }
}
