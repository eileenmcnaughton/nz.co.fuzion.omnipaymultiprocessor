<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

class TransactionStatus extends Authorize
{
    const PARAM_PASSWORD = 'Password';

    protected $TransactionDetailsRequirement = [
        self::PARAM_ACQUIRERID => ["R",0,11],
        self::PARAM_MERCHANTID => ["R",0,15],
        self::PARAM_ORDERNUMBER => ["R",0,150]
    ];

    protected $TransactionStatus = [];

    public function getData()
    {
        $this->TransactionStatus = array_merge($this->TransactionStatus, $this->setTransactionDetailsCommon());
        $this->TransactionStatus[self::PARAM_PASSWORD] = $this->getFacPwd();
        $this->data = $this->TransactionStatus;

        return $this->data;
    }
}