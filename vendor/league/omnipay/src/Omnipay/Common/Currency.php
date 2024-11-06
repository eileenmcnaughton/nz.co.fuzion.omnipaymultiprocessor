<?php

namespace Omnipay\Common;

/**
 * Currency class
 */
class Currency
{
    private $code;
    private $numeric;
    private $decimals;

    /**
     * Create a new Currency object
     */
    private function __construct($code, $numeric, $decimals)
    {
        $this->code = $code;
        $this->numeric = $numeric;
        $this->decimals = $decimals;
    }

    /**
     * Get the three letter code for the currency
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the numeric code for this currency
     *
     * @return string
     */
    public function getNumeric()
    {
        return $this->numeric;
    }

    /**
     * Get the number of decimal places for this currency
     *
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * Find a specific currency
     *
     * @param  string $code The three letter currency code
     * @return mixed  A Currency object, or null if no currency was found
     */
    public static function find($code)
    {
        $code = strtoupper($code);
        $currencies = static::all();

        if (isset($currencies[$code])) {
            return new static($code, $currencies[$code]['numeric'], $currencies[$code]['decimals']);
        }
    }

    /**
     * Get an array of all supported currencies
     *
     * @return array
     */
    public static function all()
    {
        return array(
            'AUD' => array('numeric' => '036', 'decimals' => 2),
            'BRL' => array('numeric' => '986', 'decimals' => 2),
            'CAD' => array('numeric' => '124', 'decimals' => 2),
            'CHF' => array('numeric' => '756', 'decimals' => 2),
            'CLP' => array('numeric' => '152', 'decimals' => 0),
            'CNY' => array('numeric' => '156', 'decimals' => 2),
            'CZK' => array('numeric' => '203', 'decimals' => 2),
            'DKK' => array('numeric' => '208', 'decimals' => 2),
            'EUR' => array('numeric' => '978', 'decimals' => 2),
            'FJD' => array('numeric' => '242', 'decimals' => 2),
            'GBP' => array('numeric' => '826', 'decimals' => 2),
            'HKD' => array('numeric' => '344', 'decimals' => 2),
            'HUF' => array('numeric' => '348', 'decimals' => 2),
            'ILS' => array('numeric' => '376', 'decimals' => 2),
            'INR' => array('numeric' => '356', 'decimals' => 2),
            'JPY' => array('numeric' => '392', 'decimals' => 0),
            'KRW' => array('numeric' => '410', 'decimals' => 0),
            'LAK' => array('numeric' => '418', 'decimals' => 0),
            'MXN' => array('numeric' => '484', 'decimals' => 2),
            'MYR' => array('numeric' => '458', 'decimals' => 2),
            'NOK' => array('numeric' => '578', 'decimals' => 2),
            'NZD' => array('numeric' => '554', 'decimals' => 2),
            'PGK' => array('numeric' => '598', 'decimals' => 2),
            'PHP' => array('numeric' => '608', 'decimals' => 2),
            'PLN' => array('numeric' => '985', 'decimals' => 2),
            'SBD' => array('numeric' => '090', 'decimals' => 2),
            'SEK' => array('numeric' => '752', 'decimals' => 2),
            'SGD' => array('numeric' => '702', 'decimals' => 2),
            'THB' => array('numeric' => '764', 'decimals' => 2),
            'TOP' => array('numeric' => '776', 'decimals' => 2),
            'TRY' => array('numeric' => '949', 'decimals' => 2),
            'TWD' => array('numeric' => '901', 'decimals' => 2),
            'USD' => array('numeric' => '840', 'decimals' => 2),
            'VND' => array('numeric' => '704', 'decimals' => 0),
            'VUV' => array('numeric' => '548', 'decimals' => 0),
            'WST' => array('numeric' => '882', 'decimals' => 2),
            'ZAR' => array('numeric' => '710', 'decimals' => 2),
        );
    }
}
