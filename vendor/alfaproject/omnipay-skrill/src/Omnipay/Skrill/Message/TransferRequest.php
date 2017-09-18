<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Transfer Request
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
class TransferRequest extends Request
{
    /**
     * {@inheritdoc}
     */
    protected function getEndpoint()
    {
        return 'https://www.skrill.com/app/pay.pl';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAction()
    {
        return 'transfer';
    }

    /**
     * Get the session identifier from the previous step.
     *
     * @return string session id
     */
    public function getSessionId()
    {
        return $this->getParameter('sessionId');
    }

    /**
     * Set the session identifier from the previous step.
     *
     * @param string $value session id
     * @return self
     */
    public function setSessionId($value)
    {
        return $this->setParameter('sessionId', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // make sure we have the mandatory fields
        $this->validate('sessionId');

        $data = parent::getData();
        $data['sid'] = $this->getSessionId();

        return $data;
    }
}
