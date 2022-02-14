<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 * 
 * Some constants used within the project.
 */
namespace Omnipay\FirstAtlanticCommerce;

class Constants
{
    const DRIVER_NAME = 'First Atlantic Commerce - Payment Gateway';

    const PLATFORM_XML_UAT = 'https://ecm.firstatlanticcommerce.com/PGServiceXML/';
    const PLATFORM_XML_PROD = 'https://marlin.firstatlanticcommerce.com/PGServiceXML/';
    const PLATFORM_MERCHANT_PAGES_UAT = 'https://ecm.firstatlanticcommerce.com/MerchantPages/';
    const PLATFORM_MERCHANT_PAGES_PROD = 'https://marlin.firstatlanticcommerce.com/MerchantPages/';

    const PLATFORM_XML_NS = "http://schemas.firstatlanticcommerce.com/gateway/data";

    const CONFIG_KEY_FACID = 'facId';
    const CONFIG_KEY_FACPWD = 'facPwd';
    const CONFIG_KEY_FACAQID = 'facAcquirer';
    const CONFIG_KEY_FACCUR = 'facCurrencyList';
    const CONFIG_KEY_FACPGSET = 'facPageSet';
    const CONFIG_KEY_FACPGNAM = 'facPageName';
    const CONFIG_KEY_MERCHANT_RESPONSE_URL = 'merchantResponseURL';

    const AUTHORIZE_OPTION_3DS = '3DS';
    const AUTHORIZE_OPTION_HOSTED_PAGE = 'HPA';

    const GATEWAY_INTEGRATION_DIRECT = 'direct';
    const GATEWAY_INTEGRATION_HOSTED = 'hosted';
    const GATEWAY_CONFIG_KEY_INTEGRATION = 'integrationOption';
    const GATEWAY_ORDER_NUMBER_PREFIX = 'orderNumberPrefix';
    const GATEWAY_ORDER_NUMBER_AUTOGEN = 'orderNumberAutoGen';
}