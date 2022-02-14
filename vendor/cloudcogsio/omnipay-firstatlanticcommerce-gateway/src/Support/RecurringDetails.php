<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Exception\InvalidRecurringFrequencyOption;

class RecurringDetails extends AbstractSupport
{
    const PARAM_IS_RECURRING = "IsRecurring";
    const PARAM_EXECUTION_DATE = "ExecutionDate";
    const PARAM_FREQUENCY = "Frequency";
    const PARAM_NO_OF_RECURRENCES = "NumberOfRecurrences";

    const FREQUENCY_DAILY = "D";
    const FREQUENCY_WEEKLY = "W";
    const FREQUENCY_FORTNIGHTLY = "F";
    const FREQUENCY_MONTHLY = "M";
    const FREQUENCY_BI_MONTHLY = "E";
    const FREQUENCY_QUARTERLY = "Q";
    const FREQUENCY_YEARLY = "Y";

    protected $frequency = [
        self::FREQUENCY_DAILY,
        self::FREQUENCY_WEEKLY,
        self::FREQUENCY_FORTNIGHTLY,
        self::FREQUENCY_MONTHLY,
        self::FREQUENCY_BI_MONTHLY,
        self::FREQUENCY_QUARTERLY,
        self::FREQUENCY_YEARLY
    ];

    protected $data = [
        self::PARAM_EXECUTION_DATE => null,
        self::PARAM_FREQUENCY => null,
        self::PARAM_IS_RECURRING => false,
        self::PARAM_NO_OF_RECURRENCES => 0
    ];

    public function setIsRecurring(bool $value)
    {
        return $this->setData([self::PARAM_IS_RECURRING => ($value)?"True":"False"]);
    }

    public function getIsRecurring() : bool
    {
        return ($this->data[self::PARAM_IS_RECURRING] == "True")?true:false;
    }

    public function setExecutionDate($date)
    {
        return $this->setData([self::PARAM_EXECUTION_DATE => date("Ymd",strtotime($date))]);
    }

    public function getExecutionDate()
    {
        return $this->data[self::PARAM_EXECUTION_DATE];
    }

    public function setFrequency($value)
    {
        if (array_key_exists($value, $this->frequency)) return $this->setData([self::PARAM_FREQUENCY => strtoupper($value)]);
        throw new InvalidRecurringFrequencyOption($value);
    }

    public function getFrequency()
    {
        return $this->data[self::PARAM_FREQUENCY];
    }

    public function setNumberOfRecurrences($value)
    {
        if (intval($value) > 999) $value = 999;
        if (intval($value) < 0) $value = 0;

        return $this->setData([self::PARAM_NO_OF_RECURRENCES => intval($value)]);
    }

    public function getNumberOfRecurrences()
    {
        return $this->data[self::PARAM_NO_OF_RECURRENCES];
    }
}