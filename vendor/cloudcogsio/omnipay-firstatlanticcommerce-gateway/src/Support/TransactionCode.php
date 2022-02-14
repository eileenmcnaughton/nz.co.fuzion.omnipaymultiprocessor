<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

/**
 * TransactionCode Class
 * 
 * Used to set various transaction codes during transaction setup.
 * 
 * @example
 * 
 * // Pass all codes as array
 * $transactionCode = new TransactionCode([TransactionCode::AVS_CHECK]);
 * 
 * // Can add codes
 * $transactionCode->addCode(TransactionCode::TOKEN_REQUEST);
 * 
 * // Set codes for transaction
 * $gateway->authorize($options)->setTransactionCode($transactionCode);
 * 
 */
class TransactionCode
{
    const NONE = 0;
    const AVS_CHECK = 1;
    const AVS_CHECK_SPEC = 2;
    const CONTAINS_3DS_AUTH = 4;
    const SINGLE_PASS = 8;
    const AUTH_3DS_ONLY = 64;
    const TOKEN_REQUEST = 128;
    const HOSTED_PAGE_AUTH_3DS = 256;
    const FRAUD_CHECK_ONLY = 512;
    const FRAUD_TEST = 1024;
    const RECURRING_FUTURE = 2048;
    const RECURRING_INITIAL = 4096;
    const RECURRING_INITIAL_SPEC = 8192;

    protected $codeList = [
        self::NONE,
        self::AVS_CHECK,
        self::AVS_CHECK_SPEC,
        self::CONTAINS_3DS_AUTH,
        self::SINGLE_PASS,
        self::AUTH_3DS_ONLY,
        self::TOKEN_REQUEST,
        self::HOSTED_PAGE_AUTH_3DS,
        self::FRAUD_CHECK_ONLY,
        self::FRAUD_TEST,
        self::RECURRING_FUTURE,
        self::RECURRING_INITIAL,
        self::RECURRING_INITIAL_SPEC
    ];

    protected $code = 0;

    protected $userCodes = [];

    public function __construct(array $codes)
    {
        $this->appendCodes($codes);
    }

    public function getUserCodes()
    {
        return $this->userCodes;
    }

    public function getCode()
    {
        return $this->__toString();
    }

    public function addCode($code)
    {
        return $this->appendCodes([$code]);
    }

    public function hasCode($code)
    {
        if (in_array($code, $this->userCodes)) {
            return true;
        }

        return false;
    }

    protected function appendCodes(array $codes)
    {
        foreach ($codes as $code)
        {
            if (in_array($code, $this->codeList) && !in_array(intval($code), $this->userCodes))
            {
                $this->code += intval($code);
                $this->userCodes[] = intval($code);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->code;
    }
}