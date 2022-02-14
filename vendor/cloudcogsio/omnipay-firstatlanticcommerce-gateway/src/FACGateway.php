<?php
namespace Omnipay\FirstAtlanticCommerce;

use Omnipay\Common\AbstractGateway;
use Omnipay\FirstAtlanticCommerce\Exception\MethodNotSupported;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\FirstAtlanticCommerce\Message\TransactionModification;
use Omnipay\FirstAtlanticCommerce\Support\TransactionCode;

chdir(dirname(realpath(__DIR__)));

/**
 * FACGateway Class
 *
 * @author Ricardo Assing (ricardo@tsiana.ca)
 * @version 1.0
 * 
 * While this gateway can support non-3DS transactions, 3DS is now preferred and turned on by default.
 * @see ConfigArray.php
 * 
 * A returnUrl() is therefore required to capture the FAC response and it MUST be HTTPS
 * 
 * @example 3DS Direct Integration purchase
 * 
 * $gateway = Omnipay::create("FirstAtlanticCommerce_FAC");
 * $gateway
 *  ->setFacId("xxxxxxxx") // SET FAC ID
 *  ->setFacPwd("xxxxxxxx") // SET FAC PWD
 *  ->setIntegrationOption(Constants::GATEWAY_INTEGRATION_DIRECT)
 *  ->setReturnUrl("https://xxx.xxx.xxx")
 *  
 *  $options = [
 *      'amount' => 'xxx.xx',
 *      'currency' => 'USD',
 *      'card' => $cardData,
 *      'transactionId' => 'xxxxxx'
 *  ];
 *  
 *  $response = $gateway->purchase($options)->send();
 *  $response->redirect();
 *  
 *  
 *  At the returnUrl:
 *  
 *  $response = $gateway->acceptNotification($options)->send();
 *  if($response->isSuccessful())
 *  {
 *   ...
 *  }
 *
 */
class FACGateway extends AbstractGateway
implements \Omnipay\FirstAtlanticCommerce\Support\FACParametersInterface
{
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct(null,$httpRequest);
    }

    public function getName()
    {
        return Constants::DRIVER_NAME;
    }

    public function getDefaultParameters()
    {
        $config = include 'src/ConfigArray.php';
        if (array_key_exists(Constants::CONFIG_KEY_FACCUR, $config) && is_array($config[Constants::CONFIG_KEY_FACCUR]))
        {
            $config['currency'] = $config[Constants::CONFIG_KEY_FACCUR][0];
        }

        return $config;
    }
    
    /**
     * Alias for setReturnUrl($url)
     * @see \Omnipay\FirstAtlanticCommerce\FACGateway::setReturnUrl();
     * 
     * @param string $url
     * @return \Omnipay\FirstAtlanticCommerce\FACGateway
     */
    public function setMerchantResponseURL($url)
    {
        $this->setReturnUrl($url);
        return $this->setParameter(Constants::CONFIG_KEY_MERCHANT_RESPONSE_URL, $url);
    }
    
    /**
     * 
     * @return string | NULL
     */
    public function getMerchantResponseURL()
    {
        return $this->getParameter(Constants::CONFIG_KEY_MERCHANT_RESPONSE_URL);
    }

    /**
     * Authorize only transaction.
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::authorize($options)
     */
    public function authorize(array $options = []) : \Omnipay\Common\Message\AbstractRequest
    {        
        // Additional transaction codes for AVS checks etc. (if required) can be set when configuring the gateway
        // Default Transaction Code is 0
        if (!array_key_exists('transactionCode', $options))
        {
            $options['transactionCode'] = new TransactionCode([TransactionCode::NONE]);
        }

        // For Hosted Page integration
        if (array_key_exists(Constants::AUTHORIZE_OPTION_HOSTED_PAGE, $options) && $options[Constants::AUTHORIZE_OPTION_HOSTED_PAGE] === true)
        {
            return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\HostedPagePreprocess", $options);
        }

        // For Direct Integration
        // 3DS is on by default. Allow switching to non-3DS by passing 3DS option as false
        if((array_key_exists(Constants::AUTHORIZE_OPTION_3DS, $options) && $options[Constants::AUTHORIZE_OPTION_3DS] === false)) $this->set3DS(false);
        
        if ($this->get3DS() === true)
        {
            return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\Authorize3DS", $options);
        }

        // Non-3DS transactions.
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\Authorize", $options);
    }

    public function capture(array $options = [])
    {
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\TransactionModification", array_merge($options,['modificationType' => TransactionModification::MODIFICATION_TYPE_CAPTURE]));
    }

    /**
     * Authorize and Capture (single pass) transactions.
     * 
     * Direct Integration is the default integration option.
     * 
     * For Hosted Page Integration, set Constants::AUTHORIZE_OPTION_HOSTED_PAGE = true in the $options parameter.
     * Hosted Page requires further configuration before sending.
     * @see \Omnipay\FirstAtlanticCommerce\Message\HostedPagePreprocess
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::purchase($options)
     */
    public function purchase(array $options = [])
    {
        // Add the required FAC transaction code for single pass.
        // Additional transaction codes for AVS checks etc. (if required) can be set when configuring the gateway
        
        if(array_key_exists('transactionCode', $options) && !($options['transactionCode'])->hasCode(TransactionCode::SINGLE_PASS))
        {
            ($options['transactionCode'])->addCode(TransactionCode::SINGLE_PASS);
        }

        if (!array_key_exists('transactionCode', $options))
        {
            $options['transactionCode'] = new TransactionCode([TransactionCode::SINGLE_PASS]);
        }

        // For Hosted Page integration
        if (array_key_exists(Constants::AUTHORIZE_OPTION_HOSTED_PAGE, $options) && $options[Constants::AUTHORIZE_OPTION_HOSTED_PAGE] === true)
        {
            return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\HostedPagePreprocess", $options);
        }

        // For Direct Integration
        // 3DS is on by default. Allow switching to non-3DS by passing 3DS option as false
        if((array_key_exists(Constants::AUTHORIZE_OPTION_3DS, $options) && $options[Constants::AUTHORIZE_OPTION_3DS] === false)) $this->set3DS(false);
        
        if ($this->get3DS() === true)
        {
            return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\Authorize3DS", $options);
        }

        // Non-3DS transactions.
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\Authorize", $options);
    }

    /**
     * 
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function hostedPageResults(array $options = [])
    {
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\HostedPageResults", $options);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::refund($options)
     */
    public function refund(array $options = [])
    {
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\TransactionModification", array_merge($options,['modificationType' => TransactionModification::MODIFICATION_TYPE_REFUND]));
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::void($options)
     */
    public function void(array $options = [])
    {
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\TransactionModification", array_merge($options,['modificationType' => TransactionModification::MODIFICATION_TYPE_REVERSAL]));
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::fetchTransaction($options)
     */
    public function fetchTransaction(array $options = [])
    {
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\TransactionStatus", $options);
    }
    
    /**
     * acceptNotification ONLY handles 3DS transactions. 
     * An instance of \OmniPay\FirstAtlanticCommerce\Support\ThreeDSResponse is returned for $gateway->acceptNotification($options)->send()
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\GatewayInterface::acceptNotification($options)
     */
    public function acceptNotification(array $options = [])
    {
        if (!array_key_exists("FacPwd", $options))
        {
            $options = array_merge($options,['FacPwd' => $this->getFacPwd()]);
        }
        
        return $this->createRequest("\Omnipay\FirstAtlanticCommerce\Message\AcceptNotification", $options);
    }
    
    /**
     * returnUrl will be used to capture the 3DS transaction response.
     * It will also configure the MerchantResponseURL option of the gateway which is required by FAC.
     * MerchantResponseURL can be set directly using setMerchantResponseURL($url), but using setReturnUrl($url) is preferred to maintain compatibility with Omnipay.
     * 
     * @param string $url
     * @return \Omnipay\FirstAtlanticCommerce\FACGateway
     */
    public function setReturnUrl($url)
    {
        $this->setMerchantResponseURL($url);
        return $this->setParameter("returnUrl", $url);
    }
    
    /**
     * 
     * @return string | NULL
     */
    public function getReturnUrl()
    {
        return $this->getParameter("returnUrl");
    }
    
    public function setFacId($FACID)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACID, $FACID);
    }
    
    public function getFacId()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACID);
    }
    
    public function setFacPwd($PWD)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACPWD, $PWD);
    }
    
    public function getFacPwd()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACPWD);
    }
    
    public function setFacAcquirer($ACQ)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACAQID, $ACQ);
    }
    
    public function getFacAcquirer()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACAQID);
    }
    
    public function setFacCurrencyList($list)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACCUR, $list);
    }
    
    public function getFacCurrencyList()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACCUR);
    }
    
    public function setIntegrationOption($option)
    {
        return $this->setParameter(Constants::GATEWAY_CONFIG_KEY_INTEGRATION,$option);
    }
    
    public function getIntegrationOption()
    {
        return $this->getParameter(Constants::GATEWAY_CONFIG_KEY_INTEGRATION);
    }
    
    public function setFacPageSet($PageSet)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACPGSET, $PageSet);
    }
    
    public function getFacPageSet()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACPGSET);
    }
    
    public function setFacPageName($PageName)
    {
        return $this->setParameter(Constants::CONFIG_KEY_FACPGNAM, $PageName);
    }
    
    public function getFacPageName()
    {
        return $this->getParameter(Constants::CONFIG_KEY_FACPGNAM);
    }
    
    public function set3DS($value)
    {
        return $this->setParameter(Constants::AUTHORIZE_OPTION_3DS, $value);
    }
    
    public function get3DS()
    {
        return $this->getParameter(Constants::AUTHORIZE_OPTION_3DS);
    }

    public function setOrderNumberPrefix($value)
    {
        return $this->setParameter(Constants::GATEWAY_ORDER_NUMBER_PREFIX, $value);
    }

    public function getOrderNumberPrefix()
    {
        return $this->getParameter(Constants::GATEWAY_ORDER_NUMBER_PREFIX);
    }

    public function setOrderNumberAutoGen($value)
    {
        return $this->setParameter(Constants::GATEWAY_ORDER_NUMBER_AUTOGEN, $value);
    }

    public function getOrderNumberAutoGen()
    {
        return $this->getParameter(Constants::GATEWAY_ORDER_NUMBER_AUTOGEN);
    }

    //TODO Add support for PAN Tokenization
    public function createCard(array $options = [])
    {
        throw new MethodNotSupported(__METHOD__);
    }

    //TODO Add support for PAN Tokenization
    public function updateCard(array $options = [])
    {
        throw new MethodNotSupported(__METHOD__);
    }

    //TODO Add support for PAN Tokenization
    public function deleteCard(array $options = [])
    {
        throw new MethodNotSupported(__METHOD__);
    }
}