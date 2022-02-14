<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Exception\InvalidTransactionModificationType;

class TransactionModification extends Authorize
{
    const PARAM_PASSWORD = 'Password';
    const PARAM_MODIFICATIONTYPE = 'ModificationType';

    const MODIFICATION_TYPE_CAPTURE = 1;
    const MODIFICATION_TYPE_REFUND = 2;
    const MODIFICATION_TYPE_REVERSAL = 3;
    const MODIFICATION_TYPE_CANCEL_RECURRING = 4;

    protected $TransactionDetailsRequirement = [
        self::PARAM_ACQUIRERID => ["R",0,11],
        self::PARAM_MERCHANTID => ["R",0,15],
        self::PARAM_ORDERNUMBER => ["R",0,150],
        self::PARAM_AMOUNT => ["R",0,12],
        self::PARAM_CURRENCY_EXPONENT => ["R",0,1],
        self::PARAM_MODIFICATIONTYPE => ["R",0,1]
    ];

    protected $TransactionModificationTypes = [
        self::MODIFICATION_TYPE_CAPTURE,
        self::MODIFICATION_TYPE_REFUND,
        self::MODIFICATION_TYPE_REVERSAL,
        self::MODIFICATION_TYPE_CANCEL_RECURRING
    ];

    protected $TransactionModification = [];

    public function getData()
    {
        $this->TransactionModification = array_merge($this->TransactionModification, $this->setTransactionDetailsCommon());
        $this->TransactionModification[self::PARAM_AMOUNT] = $this->getAmountForFAC();
        $this->TransactionModification[self::PARAM_CURRENCY_EXPONENT] = $this->getCurrencyDecimalPlaces();
        $this->TransactionModification[self::PARAM_PASSWORD] = $this->getFacPwd();
        $this->TransactionModification[self::PARAM_MODIFICATIONTYPE] = $this->getModificationType();

        $this->data = $this->TransactionModification;

        return $this->data;
    }

    public function setModificationType($modificationType)
    {
        if (!in_array($modificationType, $this->TransactionModificationTypes)) throw new InvalidTransactionModificationType();

        return $this->setParameter(self::PARAM_MODIFICATIONTYPE, $modificationType);
    }

    public function getModificationType()
    {
        return $this->getParameter(self::PARAM_MODIFICATIONTYPE);
    }
}