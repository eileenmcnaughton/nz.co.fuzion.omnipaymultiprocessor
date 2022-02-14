<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

class FraudDetails extends AbstractSupport
{
    const PARAM_AUTH_RESPONSE_CODE = "AuthResponseCode";
    const PARAM_AVS_RESPONSE_CODE = "AVSResponseCode";
    const PARAM_CVV_RESPONSE_CODE = "CVVResponseCode";
    const PARAM_SESSION_ID = "SessionId";

    const AUTH_RESPONSE_CODE_AUTH = "A";
    const AUTH_RESPONSE_CODE_DECLINED = "D";

    protected $data = [
        self::PARAM_AUTH_RESPONSE_CODE => null,
        self::PARAM_AVS_RESPONSE_CODE => null,
        self::PARAM_CVV_RESPONSE_CODE => null,
        self::PARAM_SESSION_ID => null
    ];

    public function getAVSResponseCode($CardBrand) : AVSCheckResponse
    {
        return new AVSCheckResponse($CardBrand, $this->data[self::PARAM_AVS_RESPONSE_CODE]);
    }

    public function getCVVResponseCode() : CVVResponse
    {
        return new CVVResponse($this->data[self::PARAM_CVV_RESPONSE_CODE]);
    }
}