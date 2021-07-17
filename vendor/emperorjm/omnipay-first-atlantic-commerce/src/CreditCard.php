<?php

namespace Omnipay\FirstAtlanticCommerce;

use League\ISO3166\ISO3166;
use Omnipay\Common\CreditCard as BaseCreditCard;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Helper;


class CreditCard extends BaseCreditCard
{
    /**
     * Validate this credit card. If the card is invalid, InvalidCreditCardException is thrown.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the credit card is clearly invalid.
     *
     * Falls back to validating number, cvv, expiryMonth, expiryYear if no parameters are present.
     *
     * @param string ... Optional variable length list of required parameters
     * @throws InvalidCreditCardException
     */
    public function validate()
    {
        $parameters = func_get_args();

        if ( count($parameters) == 0 )
        {
            $parameters = ['number', 'cvv', 'expiryMonth', 'expiryYear'];
        }

        foreach ($parameters as $key)
        {
            $value = $this->parameters->get($key);

            if ( empty($value) )
            {
                throw new InvalidCreditCardException("The $key parameter is required");
            }
        }

        if ( isset($parameters['expiryMonth']) && isset($parameters['expiryYear']) )
        {
            if ( $this->getExpiryDate('Ym') < gmdate('Ym') )
            {
                throw new InvalidCreditCardException('Card has expired');
            }
        }

        if ( isset($parameters['number']) )
        {
            if ( !Helper::validateLuhn( $this->getNumber() ) )
            {
                throw new InvalidCreditCardException('Card number is invalid');
            }

            if ( !is_null( $this->getNumber() ) && !preg_match( '/^\d{12,19}$/i', $this->getNumber() ) )
            {
                throw new InvalidCreditCardException('Card number should have 12 to 19 digits');
            }
        }

        if ( isset($parameters['cvv']) )
        {
            if ( !is_null( $this->getCvv() ) && !preg_match( '/^\d{3,4}$/i', $this->getCvv() ) )
            {
                throw new InvalidCreditCardException('Card CVV should have 3 to 4 digits');
            }
        }
    }

    /**
     * Returns the country as the numeric ISO 3166-1 code
     *
     * @throws InvalidRequestException
     *
     * @return int ISO 3166-1 numeric country
     */
    public function getNumericCountry()
    {
        $country = $this->getCountry();

        if ( !is_null($country) && !is_numeric($country) )
        {
            $iso3166 = new ISO3166();

            if ( strlen($country) == 2 )
            {
                $country = $iso3166->getByAlpha2($country)['numeric'];
            }
            elseif ( strlen($country) == 3 )
            {
                $country = $iso3166->getByAlpha3($country)['numeric'];
            }
            else
            {
                throw new InvalidRequestException("The country parameter must be ISO 3166-1 numeric, aplha2 or alpha3.");
            }
        }

        return $country;
    }

    /**
     * Returns the billing state if its a US abbreviation or throws an exception
     *
     * @throws InvalidRequestException
     *
     * @return string State abbreviation
     */
    public function validateState()
    {
        $state = $this->getState();

        if ( strlen($state) != 2 )
        {
            throw new InvalidRequestException("The state must be a two character abbreviation.");
        }

        return $state;
    }

    /**
     * Returns the postal code sanitizing dashes and spaces and throws exceptions with other
     * non-alphanumeric characters
     *
     * @throws InvalidRequestException
     *
     * @return string Postal code
     */
    public function formatPostcode()
    {
        $postal = preg_replace( '/[\s\-]/', '', $this->getPostcode() );

        if ( preg_match('/[^a-z0-9]/i', $postal) )
        {
            throw new InvalidRequestException("The postal code must be alpha-numeric.");
        }

        return $postal;
    }

}
