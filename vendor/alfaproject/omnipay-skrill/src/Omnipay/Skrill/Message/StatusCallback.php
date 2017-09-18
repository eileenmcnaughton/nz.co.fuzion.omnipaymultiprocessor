<?php

namespace Omnipay\Skrill\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Skrill Status Callback
 *
 * When the payment process is complete Skrill's payment server will send the details of
 * the transaction to the status URL provided by the merchant. This is done with a
 * standard HTTP POST request. The Skrill server will continue to post the status reports
 * until a response of HTTP OK (200) is received from the merchant's server or the number
 * of posts exceeds 10.
 *
 * @author    Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   6.19 Merchant Integration Manual
 */
class StatusCallback extends AbstractResponse
{
    /**
     * This status could be received only if the merchant's account is configured to
     * receive chargebacks. If this is the case, whenever a chargeback is received by
     * Skrill, a -3 status will be posted on the status url for the reversed transaction.
     */
    const STATUS_CHARGEBACK = -3;

    /**
     * This status is sent when the customer tries to pay via Credit Card or Direct Debit
     * but our provider declines the transaction. If the merchant doesn't accept Credit
     * Card or Direct Debit payments via Skrill then you will never receive the failed
     * status.
     */
    const STATUS_FAILED = -2;

    /**
     * Pending transactions can either be cancelled manually by the sender in their
     * online account history or they will auto-cancel after 14 days if still pending.
     */
    const STATUS_CANCELLED = -1;

    /**
     * This status is sent when the customers pay via the pending bank transfer option.
     * Such transactions will auto-process IF the bank transfer is received by Skrill. We
     * strongly recommend that you do NOT process the order/transaction in your system
     * upon receipt of a pending status from Skrill.
     */
    const STATUS_PENDING = 0;

    /**
     * This status is sent when the transaction is processed and the funds have been
     * received on the merchant's Skrill account.
     */
    const STATUS_PROCESSED = 2;

    /**
     * Construct a StatusCallback with the respective POST data.
     *
     * @param array $post post data
     */
    public function __construct(array $post)
    {
        $this->data = $post;
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if (!$this->testMdSignatures()) {
            return false;
        }

        return in_array($this->getStatus(), [self::STATUS_PENDING, self::STATUS_PROCESSED]);
    }

    /**
     * Was the payment cancelled?
     *
     * @return bool
     */
    public function isCancelled()
    {
        if (!$this->testMdSignatures()) {
            return false;
        }

        return in_array($this->getStatus(), [self::STATUS_CHARGEBACK, self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Validates the MD5 signature and, if enabled, the SHA2 signature.
     *
     * @return bool
     */
    protected function validateSignatures()
    {
        if (!$this->testMdSignatures()) {
            return false;
        }

        if ($this->getSha2Signature() !== null) {
            return $this->getSha2Signature() === $this->calculateSha2Signature();
        }

        return true;
    }

    /**
     * @see getStatus() for the possible status codes.
     *
     * @return int status
     */
    public function getCode()
    {
        return $this->getStatus();
    }

    /**
     * Get the status of the transaction.
     *
     * * -3 - Chargeback (see STATUS_CHARGEBACK)
     * * -2 - Failed (see STATUS_FAILED)
     * * -1 - Cancelled (see STATUS_CANCELLED)
     * * 0 - Pending (see STATUS_PENDING)
     * * 2 - Processed (see STATUS_PROCESSED)
     *
     * @return int status
     */
    public function getStatus()
    {
        return (int)$this->data['status'];
    }

    /**
     * Get the merchant's email address.
     *
     * @return string merchant's email
     */
    public function getMerchantEmail()
    {
        return $this->data['pay_to_email'];
    }

    /**
     * Get the email address of the customer who is making the payment, i.e. sending the
     * money.
     *
     * @return string customer's email
     */
    public function getCustomerEmail()
    {
        return $this->data['pay_from_email'];
    }

    /**
     * Get the unique ID for the merchant's Skrill account.
     *
     * ONLY needed for the calculation of the MD5 signature.
     *
     * @return int merchant's id
     */
    public function getMerchantId()
    {
        return (int)$this->data['merchant_id'];
    }

    /**
     * Get the unique ID for the customer's Skrill account.
     *
     * To receive the customer id value, please contact your account manager or
     * merchantservices@skrill.com
     *
     * @return int customer's id
     */
    public function getCustomerId()
    {
        return (int)$this->data['customer_id'] ?: null;
    }

    /**
     * Get the unique reference or identification number provided by the merchant.
     *
     * @return string transaction id
     */
    public function getTransactionId()
    {
        return $this->data['transaction_id'] ?: $this->getTransactionReference();
    }

    /**
     * Get Skrill's unique transaction reference for the transfer.
     *
     * @return string transaction reference
     */
    public function getTransactionReference()
    {
        return $this->data['mb_transaction_id'];
    }

    /**
     * Get the total amount of the payment in merchant's currency.
     *
     * @param $stringFormat
     * @return float|string amount
     */
    public function getSkrillAmount($stringFormat = false)
    {
        $amount = (double)$this->data['mb_amount'];

        if ($stringFormat) {
            $amount = number_format($amount, 2, '.', '');
        }

        return $amount;
    }

    /**
     * Get the currency of skrill amount.
     *
     * Will always be the same as the currency of the beneficiary's account at Skrill.
     *
     * @return string currency
     */
    public function getSkrillCurrency()
    {
        return $this->data['mb_currency'];
    }

    /**
     * Get the code detailing the reason for the failure, if the transaction is with
     * status -2 (failed).
     *
     * To receive the failed reason code value, please contact your account manager or
     * merchantservices@skrill.com.
     *
     * @return int failed reason code
     */
    public function getFailedReasonCode()
    {
        return (int)$this->data['failed_reason_code'] ?: null;
    }

    /**
     * Get the MD5 signature.
     *
     * @return string md5 signature
     */
    public function getMd5Signature()
    {
        return $this->data['md5sig'];
    }

    /**
     * Get the SHA2 signature.
     *
     * To enable the sha2sig parameter, please contact merchantservices@skrill.com.
     *
     * @return string sha2 signature
     */
    public function getSha2Signature()
    {
        return isset($this->data['sha2sig']) ? $this->data['sha2sig'] : null;
    }

    /**
     * Get the amount of the payment as posted by the merchant on the entry form.
     *
     * @return double amount
     */
    public function getAmount()
    {
        return (double)$this->data['amount'];
    }

    /**
     * Get the currency of the payment as posted by the merchant on the entry form.
     *
     * @return string currency
     */
    public function getCurrency()
    {
        return $this->data['currency'];
    }

    /**
     * Get the payment instrument used by the customer on the gateway.
     *
     * The merchant can choose to receive:
     *
     * * consolidated values (only the type of the instrument, e.g. MBD - MB Direct,
     *   WLT - e-wallet or PBT - pending bank transfer)
     * * detailed values (the specific instrument used, e.g. VSA - Visa card,
     *   GIR - Giropay, etc.)
     *
     * To receive the payment type value, please contact your account manager or
     * merchantservices@skrill.com.
     *
     * @return string payment type
     */
    public function getPaymentType()
    {
        return $this->data['payment_type'] ?: null;
    }

    /**
     * Get the fields that the merchant chose to submit in the merchant_fields parameter.
     *
     * @param  array $keys keys for the fields
     *
     * @return array         merchant fields
     */
    public function getMerchantFields(array $keys)
    {
        $fields = [];
        foreach ($keys as $key) {
            $fields[$key] = $this->data[$key];
        }

        return $fields;
    }

    /**
     * Calculate the 128 bit message digest, expressed as a string of thirty-two
     * hexadecimal digits in UPPERCASE.
     *
     * The md5sig is constructed by performing a MD5 calculation on a string built up by
     * concatenating the other fields returned to the status url.
     *
     * @return string               md5 signature
     */
    public function calculateMd5Signature()
    {
        return strtoupper(md5(
            $this->getMerchantId() .
            $this->getTransactionReference() .
            $this->getSecretWordForMd5Signature() .
            $this->getSkrillAmount(true) .
            $this->getSkrillCurrency() .
            $this->getStatus()
        ));
    }

    /**
     * Calculate the 256 bit message digest, expressed as a string of sixty-four
     * hexadecimal digits in lowercase.
     *
     * The sha2sig is constructed by performing a SHA256 calculation on a string built up
     * by concatenating the other fields returned to the status url.
     *
     * @return string               sha2 signature
     */
    public function calculateSha2Signature()
    {
        return hash('sha256',
            $this->getMerchantId() .
            $this->getTransactionReference() .
            $this->getSecretWordForMd5Signature() .
            $this->getSkrillAmount() .
            $this->getSkrillCurrency() .
            $this->getStatus()
        );
    }

    /**
     * Get the merchant's email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return isset($this->data['email']) ? $this->data['email'] : null;
    }

    /**
     * Get the merchant's MD5 API/MQI password.
     *
     * @return string password
     */
    public function getPassword()
    {
        return isset($this->data['password']) ? $this->data['password'] : null;
    }

    /**
     * Get the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * @return string notify url
     */
    public function getNotifyUrl()
    {
        return isset($this->data['notifyUrl']) ? $this->data['notifyUrl'] : null;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if (!$this->testMdSignatures()) {
            return "MD5 signature {$this->calculateMd5Signature()} ({$this->data['secretWord']}) doesn't match {$this->getMd5Signature()}";
        } else {
            return parent::getMessage();
        }
    }

    /**
     * @return bool
     */
    public function testMdSignatures()
    {
        return (! $this->getSecretWord()) || $this->getMd5Signature() == $this->calculateMd5Signature();
    }

    /**
     * @param string $secretWord
     * @return $this
     */
    public function setSecretWord($secretWord)
    {
        $this->data['secretWord'] = $secretWord;

        return $this;
    }

    public function getSecretWord()
    {
        return isset($this->data['secretWord']) ? $this->data['secretWord'] : null;
    }

    /**
     * @return string
     */
    protected function getSecretWordForMd5Signature()
    {
        return strtoupper($this->getSecretWord());
    }

}
