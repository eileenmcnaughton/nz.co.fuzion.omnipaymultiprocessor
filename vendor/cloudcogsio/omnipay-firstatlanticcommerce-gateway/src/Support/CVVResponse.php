<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Exception\UnsupportedCVVResponseCode;

class CVVResponse
{
    protected $responseCodes = [
        "M" => "Match",
        "N" => "No Match",
        "P" => "Not Processed",
        "S" => "Should be on card but was not provided. (Visa only)",
        "U" => "Issuer not participating or certified"
    ];

    protected $responseCode;

    public function __construct($responseCode)
    {
        $this->setResponseCode($responseCode);
    }

    public function setResponseCode($responseCode)
    {
        $responseCode = strtoupper($responseCode);
        if (array_key_exists($responseCode, $this->responseCodes))
        {
            $this->responseCode = (object) [
                'Code' => $responseCode,
                'Definition' => $this->responseCodes[$responseCode]
            ];

            return $this;
        }

        throw new UnsupportedCVVResponseCode($responseCode);
    }

    public function __toString()
    {
        if($this->responseCode)
            return $this->responseCode->Code;

        return "";
    }

    public function getResponseText()
    {
        if ($this->responseCode)
        {
            return $this->responseCode->Definition;
        }
    }

    public function getResponseCode()
    {
        if ($this->responseCode)
        {
            return $this->responseCode->Code;
        }
    }
}