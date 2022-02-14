<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Exception\UnsupportedBrand;
use Omnipay\FirstAtlanticCommerce\Exception\UnsupportedAVSCheckResponseCode;

class AVSCheckResponse
{
    const BRAND_VISA = "visa";
    const BRAND_MASTERCARD = "mastercard";
    const BRAND_AMEX = "amex";

    protected $responseCode;

    protected $responseCodes = [
        self::BRAND_VISA => [
            "A" => "Address matches, Zip code does not match.",
            "B" => "Street addresses match for international transaction. Postal code not verified due to incompatible formats.",
            "C" => "Street address and postal code not verified for international transaction due to incompatible formats.",
            "D" => "Street addresses and postal codes match for international transaction.",
            "E" => "Error response for Merchant Category Code.",
            "F" => "Address does compare and five-digit ZIP codes does compare (UK only)",
            "G" => "Address information is unavailable for international transaction; non-AVS participant.",
            "I" => "Address information not verified for international transaction.",
            "M" => "Street addresses and postal codes match for international transaction.",
            "N" => "Address and ZIP code do not match.",
            "P" => "Postal codes match for international transaction. Street address not verified due to incompatible formats.",
            "R" => "Retry; system unavailable or timed out.",
            "S" => "Service not supported by issuer.",
            "U" => "Address information is unavailable; domestic transactions.",
            "W" => "Nine-digit ZIP code matches, but address does not match.",
            "X" => "Exact match, address, and nine-digit ZIP code match.",
            "Y" => "Address and five-digit ZIP code match.",
            "Z" => "Five-digit ZIP code matches, but address does not match.",
            "5" => "Invalid AVS response (from VISA).",
            "9" => "Address Verification Data contains EDIT ERROR.",
            "0" => "Issuer has chosen not to perform Address Verification for an authorization that was declined.",
        ],
        self::BRAND_MASTERCARD => [
            "A" => "Address matches, postal code does not.",
            "N" => "Neither address nor postal code matches.",
            "R" => "Retry, system unable to process.",
            "S" => "AVS currently not supported",
            "U" => "No data from issuer/Authorization System.",
            "W" => "For U.S. addresses, nine-digit postal code matches, address does not; for address outside the U.S., postal code matches, address does not.",
            "X" => "For U.S. addresses, nine-digit postal code and address matches; for addresses outside the U.S., postal code and address match.",
            "Y" => "For U.S. addresses, five-digit postal code and address matches.",
            "Z" => "For U.S. addresses, five-digit postal code matches, address does not.",
            "5" => "Invalid AVS response (from MasterCard)",
            "9" => "Address Verification Data contains EDIT ERROR.",
            "0" => "Issuer has chosen not to perform Address Verification for an authorization that was declined.",
        ],
        self::BRAND_AMEX => [
            "A" => "ADDRESS: Address correct, zip code incorrect",
            "N" => "NO: Address and zip code are no correct.",
            "R" => "Retry, system unavailable or timeout.",
            "S" => "Address Verification Service not valid.",
            "U" => "Address information is unavailable, account number is not US or Canadian.",
            "Y" => "YES: Address and zip code are correct.",
            "Z" => "Zip code correct; address incorrect.",
            "5" => "Invalid AVS response (from American Express).",
            "9" => "Address Verification Data contains EDIT ERROR."
        ]
    ];

    public function __construct($brand, $responseCode)
    {
        $this->setResponseCode($brand, $responseCode);
    }

    public function setResponseCode($brand, $responseCode)
    {
        $responseCode = strtoupper($responseCode);

        if(!array_key_exists($brand, $this->responseCodes))
        {
            throw new UnsupportedBrand($brand);
        }


        if (array_key_exists($responseCode, $this->responseCodes[$brand]))
        {
            $this->responseCode = (object) [
                'Code' => $responseCode,
                'Definition' => $this->responseCodes[$brand][$responseCode]
            ];

            return $this;
        }

        throw new UnsupportedAVSCheckResponseCode($responseCode);
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
}