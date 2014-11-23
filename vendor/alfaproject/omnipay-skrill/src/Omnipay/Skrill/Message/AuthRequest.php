<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Auth Request
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Skrill Automated Payments Interface
 */
abstract class AuthRequest extends Request
{
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
     * @return self
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        // make sure we have the mandatory fields
        $this->validate('email', 'password');

        $data = parent::getData();
        $data['email'] = $this->getEmail();
        $data['password'] = $this->getPassword();

        return $data;
    }
}
