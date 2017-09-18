<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Skrill Authorize Refund Request
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
class AuthorizeRefundRequest extends AuthRequest
{

    /**
     * {@inheritdoc}
     */
    protected function getEndpoint()
    {
        return 'https://www.skrill.com/app/refund.pl';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAction()
    {
        return 'prepare';
    }

    /**
     * Get the description of the refund.
     *
     * @return string description
     */
    public function getDescription()
    {
        return $this->getParameter('description');
    }

    /**
     * Set the description of the refund.
     *
     * @param string $value description
     * @return self
     */
    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    /**
     * Get the URL or email address to which status updates should be sent.
     *
     * @return string url or email
     */
    public function getStatusUrl()
    {
        return $this->getParameter('statusUrl');
    }

    /**
     * Get the URL or email address to which status updates should be sent.
     *
     * @param string $value url or email
     * @return self
     */
    public function setStatusUrl($value)
    {
        return $this->setParameter('statusUrl', $value);
    }

    /**
     * Get the list of fields that should be passed back to the merchant's server when
     * the refund payment is confirmed. (maximum 5 fields)
     *
     * @return array merchant fields
     */
    public function getMerchantFields()
    {
        return $this->getParameter('merchantFields');
    }

    /**
     * Get the list of fields that should be passed back to the merchant's server when
     * the refund payment is confirmed. (maximum 5 fields)
     *
     * @param array $value merchant fields
     * @return self
     */
    public function setMerchantFields($value)
    {
        return $this->setParameter('merchantFields', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // make sure we have a (skrill) transaction id
        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        if (empty($transactionReference) && empty($transactionId)) {
            throw new InvalidRequestException('Either transactionId or transactionReference is required');
        }

        $data = parent::getData();
        $data['transaction_id'] = $transactionId;
        $data['mb_transaction_id'] = $transactionReference;
        $data['amount'] = $this->getAmount();
        $data['refund_note'] = $this->getDescription();
        $data['refund_status_url'] = $this->getStatusUrl();

        // merchant fields
        $merchantFields = $this->getMerchantFields();
        $data['merchant_fields'] = implode(',', array_keys($merchantFields));
        foreach ($merchantFields as $key => $value) {
            $data[$key] = $value;
        }

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
