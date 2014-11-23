<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Transfer Response
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
class TransferResponse extends Response
{
    /**
     * Beneficiary is not yet registered at Skrill.
     */
    const STATUS_SCHEDULED = 1;

    /**
     * Beneficiary is registered at Skrill.
     */
    const STATUS_PROCESSED = 2;

    /**
     * Get the amount paid in the currency of the merchant's account.
     *
     * Example: 2.35
     *
     * @return double amount
     */
    public function getAmount()
    {
        return (double) $this->data->transaction->amount;
    }

    /**
     * Get the currency of the merchant's account.
     *
     * Example: EUR
     *
     * @return string currency
     */
    public function getCurrency()
    {
        return (string) $this->data->transaction->currency;
    }

    /**
     * Get the transaction reference.
     *
     * Example: 983115224
     *
     * @return string transaction reference
     */
    public function getTransactionReference()
    {
        return (string) $this->data->transaction->id;
    }

    /**
     * Get the numeric value of the transaction status.
     *
     * * 1 - scheduled (if beneficiary is not yet registered at Skrill);
     * * 2 - processed (if beneficiary is registered);
     *
     * @return int status code
     */
    public function getStatus()
    {
        return (int) $this->data->transaction->status;
    }

    /**
     * Get the text value of the transaction status.
     *
     * Example: processed
     *
     * @return string status message
     */
    public function getStatusMessage()
    {
        return (string) $this->data->transaction->status_msg;
    }

    /**
     * Get a code describing the status of this response.
     *
     * @return string|null code
     */
    public function getCode()
    {
        return $this->isSuccessful()
            ? $this->getStatus()
            : parent::getCode();
    }

    /**
     * Get a message describing the status of this response.
     *
     * @return string message
     */
    public function getMessage()
    {
        return $this->isSuccessful()
            ? $this->getStatusMessage()
            : parent::getMessage();
    }
}
