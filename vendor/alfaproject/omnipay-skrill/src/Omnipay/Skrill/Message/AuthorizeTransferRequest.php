<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Authorize Transfer Request
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
class AuthorizeTransferRequest extends AuthRequest
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
        return 'prepare';
    }

    /**
     * Get the subject of the notification email.
     *
     * @return string subject
     */
    public function getSubject()
    {
        return $this->getParameter('subject');
    }

    /**
     * Set the subject of the notification email.
     *
     * Example: Your order is ready
     *
     * @param string $value subject
     * @return self
     */
    public function setSubject($value)
    {
        return $this->setParameter('subject', $value);
    }

    /**
     * Get the comment to be included in the notification email.
     *
     * @return string note
     */
    public function getNote()
    {
        return $this->getParameter('note');
    }

    /**
     * Set the comment to be included in the notification email.
     *
     * Example: Details are available at our site...
     *
     * @param string $value note
     * @return self
     */
    public function setNote($value)
    {
        return $this->setParameter('note', $value);
    }

    /**
     * Get the customer email address.
     *
     * @return string customer email
     */
    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    /**
     * Set the customer email address.
     *
     * @param string $value customer email
     * @return self
     */
    public function setCustomerEmail($value)
    {
        return $this->setParameter('customerEmail', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // make sure we have the mandatory fields
        $this->validate('amount', 'currency', 'subject', 'note', 'customerEmail');

        $data = parent::getData();
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['subject'] = $this->getSubject();
        $data['note'] = $this->getNote();
        $data['bnf_email'] = $this->getCustomerEmail();
        $data['frn_trn_id'] = $this->getTransactionId();

        return $data;
    }

    /**
     * Create the authorize response for this request.
     *
     * @param  \SimpleXMLElement  $xml  raw response
     * @return AuthorizeResponse        response object for this request
     */
    protected function createResponse($xml)
    {
        return $this->response = new AuthorizeResponse($this, $xml);
    }
}
