<?php

namespace Omnipay\Gopay\Api;

class GopayConfig
{
    const PROD_FULL_URL = "https://gate.gopay.cz/gw/pay-full-v2";
    const TEST_FULL_URL = "https://testgw.gopay.cz/gw/pay-full-v2";
    const PROD_WSDL_URL = "https://gate.gopay.cz/axis/EPaymentServiceV2?wsdl";
    const TEST_WSDL_URL = "https://testgw.gopay.cz/axis/EPaymentServiceV2?wsdl";

    /**
     *  Konfiguracni trida pro ziskavani URL pro praci s platbami
     *
     */

    const TEST = "TEST";
    const PROD = "PROD";

    /**
     * Parametr specifikujici, pracuje-li se na testovacim ci provoznim prostredi
     */
    static $version = self::TEST;

    /**
     * Nastaveni testovaciho ci provozniho prostredi prostrednictvim parametru
     *
     * @param $new_version
     * TEST - Testovaci prostredi
     * PROD - Provozni prostredi
     *
     */
    public static function init($new_version)
    {
        self::$version = $new_version;
    }

    /**
     * URL webove sluzby GoPay
     *
     * @return string WSDL URL
     */
    public static function ws()
    {
        if (self::$version == self::PROD) {
            return self::PROD_WSDL_URL;

        } else {
            return self::TEST_WSDL_URL;

        }
    }
}

?>