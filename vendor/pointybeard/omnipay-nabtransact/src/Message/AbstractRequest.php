<?php

namespace Omnipay\NABTransact\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getApiVersion()
    {
        return $this->getParameter('apiVersion');
    }

    public function setApiVersion($value)
    {
        return $this->setParameter('apiVersion', $value);
    }

    abstract public function getActionType();

    public function getMessageTimestamp()
    {
        return $this->getParameter('messageTimestamp');
    }

    public function setMessageTimestamp($value)
    {
        return $this->setParameter('messageTimestamp', $value);
    }

    public function getMessageId()
    {
        return $this->getParameter('messageId');
    }

    public function setMessageId($value)
    {
        return $this->setParameter('messageId', $value);
    }

    protected function getBaseData()
    {

        $this->setMessageId(self::generateMessageId());
        $data = [
            'MessageID' => $this->getMessageId(),
            'MessageTimestamp' => null,
            'ActionType' => $this->getActionType(),
            'ApiVersion' => $this->getApiVersion(),
            'Credentials' => [
                'MerchantID' => $this->getMerchantId(),
                'Password' => $this->getPassword(),
            ],
        ];

        return $data;
    }

    protected static function generateMessageId()
    {
        $hash = hash('sha256', microtime());
        // Unfortunately NAB wants a unique string that is 30 characters long. Easist
        // way for now is to truncate the hash to 30 characters however this is not ideal
        return substr($hash, 0, 30);
    }
    /**
     * The format of the Timestamp or Log Time strings returned by NAB Transact XML API is:
     * YYYYDDMMHHnnssKKK000sOOO
     * where:
     *    YYYY = 4-digit year
     *    DD = 2-digit zero-padded day of month
     *    MM = 2-digit zero-padded month of year (January = 01)
     *    HH = 2-digit zero-padded hour of day in 24-hour clock format (midnight =0) is a 2-digit zero-padded minute of hour
     *    NN = 2-digit zero-padded second of minute
     *    SS = 3-digit zero-padded millisecond of second
     *    KKK = Static 0 characters, as NAB Transact does not store nanoseconds
     *    000 = Time zone offset, where s is “+” or “-“, and OOO = minutes, from GMT.
     *    sOOO = 4-digit year.
     *
     * E.g. June 24, 2010 5:12:16.789 PM, Australian EST is:
     *        20102406171216789000+600
     */
    protected static function generateMessageTimestamp()
    {
        list($micro, $sec) = explode(' ', microtime());
        $ss = substr($micro, 2, 3);

        return date("YdmHis{$ss}000+600");
    }

    public function getEndpointBase()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
