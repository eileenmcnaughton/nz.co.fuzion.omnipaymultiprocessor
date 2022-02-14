<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Exception\InvalidAddressState;

class USStates
{
    protected $states = [
        "AK" => "Alaska",
        "AL" => "Alabama",
        "AR" => "Arkansas",
        "AS" => "American Samoa",
        "AZ" => "Arizona",
        "CA" => "California",
        "CO" => "Colorado",
        "CT" => "Connecticut",
        "DC" => "District of Columbia",
        "DE" => "Delaware",
        "FL" => "Florida",
        "FM" => "Federate States Of Micronesia",
        "GA" => "Georgia",
        "GU" => "Guam",
        "HI" => "Hawaii",
        "IA" => "Iowa",
        "ID" => "Idaho",
        "IL" => "Illinois",
        "IN" => "Indiana",
        "KS" => "Kansas",
        "KY" => "Kentucky",
        "LA" => "Louisiana",
        "MA" => "Massachusetts",
        "MD" => "Maryland",
        "ME" => "Maine",
        "MH" => "Marshall Islands",
        "MI" => "Michigan",
        "MN" => "Minnesota",
        "MO" => "Missouri",
        "MP" => "Northern Mariana Islands",
        "MS" => "Mississippi",
        "MT" => "Montana",
        "NC" => "North Carolina",
        "ND" => "North Dakota",
        "NE" => "Nebraska",
        "NH" => "New Hampshire",
        "NJ" => "New Jersey",
        "NM" => "New Mexico",
        "NV" => "Nevada",
        "NY" => "New York",
        "OH" => "Ohio",
        "OK" => "Oklahoma",
        "OR" => "Oregon",
        "PA" => "Pennsylvania",
        "PR" => "Puerto Rico",
        "PW" => "Palau",
        "RI" => "Rhode Island",
        "SC" => "South Carolina",
        "SD" => "South Dakota",
        "TN" => "Tennessee",
        "TX" => "Texas",
        "UT" => "Utah",
        "VA" => "Virginia",
        "VI" => "U.S. Virgin Islands",
        "VT" => "Vermont",
        "WA" => "Washington",
        "WI" => "Wisconsin",
        "WV" => "West Virginia",
        "WY" => "Wyoming"
    ];

    public function getStates()
    {
        return $this->states;
    }

    public function getStateName($AB)
    {
        if (!array_key_exists(strtoupper($AB), $this->states)) throw new InvalidAddressState(strtoupper($AB));

        return $this->states[strtoupper($AB)];
    }

    /**
     * Check is two character state is valid
     * Eg. FL
     *
     * @param string(2) $state
     * @return boolean
     */
    public function isValid($state)
    {
        if (array_key_exists(strtoupper($state), $this->states)) return true;

        return false;
    }
}