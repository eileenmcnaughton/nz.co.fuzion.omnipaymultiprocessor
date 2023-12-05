<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * SystemPay Abstract Request
 */
abstract class AbstractRequest extends OmnipayAbstractRequest
{

    /**
     * Rest API endpoint
     * @var string
     */
    protected $liveEndPoint = "https://paiement.systempay.fr/vads-payment/";

    public function sendData($data)
    {
        $response = $this->httpClient->request('POST', $this->getEndPoint(), [], http_build_query($data));
        $responseData = simplexml_load_string($response->getBody()->getContents());

        return $this->createResponse($responseData);
    }

    abstract public function getEndpoint();

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return mixed
     */
    public function getTransactionDate()
    {
        return $this->getParameter('transactionDate');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setTransactionDate($value)
    {
        return $this->setParameter('transactionDate', $value);
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->getParameter('certificate');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setCertificate($value)
    {
        return $this->setParameter('certificate', $value);
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setSuccessUrl($value)
    {
        return $this->setParameter('successUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getSuccessUrl()
    {
        return $this->getParameter('successUrl');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setCancelUrl($value)
    {
        return $this->setParameter('cancelUrl', $value);
    }

    /**
     * @return mixed|string
     */
    public function getCancelUrl()
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setErrorUrl($value)
    {
        return $this->setParameter('errorUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getErrorUrl()
    {
        return $this->getParameter('errorUrl');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setRefusedUrl($value)
    {
        return $this->setParameter('refusedUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getRefusedUrl()
    {
        return $this->getParameter('refusedUrl');
    }

    /**
     * @param $value
     */
    public function setPaymentCards($value)
    {
        $this->setParameter('paymentCards', $value);
    }

    /**
     * @return mixed
     */
    public function getPaymentCards()
    {
        return $this->getParameter('paymentCards');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setUuid($value)
    {
        return $this->setParameter('vads_trans_uuid', $value);
    }

    /**
     * @return AbstractRequest
     */
    public function getUuid()
    {
        return $this->getParameter('vads_trans_uuid');
    }

    public function setMetadata(array $value)
    {
        return $this->setParameter('metadata', $value);
    }

    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }

    /**
     * @param string $amount
     * @return string
     */
    public function formatCurrency($amount)
    {
        return (string)intval(strval($amount * 100));
    }

    /**
     * @param $key
     * @param $value
     */
    public function addParameter($key, $value)
    {
        return $this->parameters->set($key, $value);
    }


    public function getAmount()
    {
        return $this->getAmountInteger()."";
    }

    /**
     * @param $value can be 'SINGLE', 'MULTI' or 'MULTI_EXT'
     * 
     * Valeurs possibles :
     * SINGLE
     * MULTI:first=montant_inital;count=nbre_echeances;period=intervalle_en_jours
     * MULTI_EXT:date1=montant1;date2=montant2;date3=montant3
     * La somme totale des montants doit être égale à la valeur du champ vads_amount.
     * 
     * @return AbstractRequest
     */
    public function setPaymentConfig($value)
    {
        return $this->setParameter('vads_payment_config', $value);
    }

    public function getPaymentConfig()
    {
        if ($this->getParameter('vads_payment_config')) {
            return $this->getParameter('vads_payment_config');
        }
        return 'SINGLE';
    }

    /**
     * Store data for recurring payments
     * We store it in the maon `token` attribute
     * as suggested in https://omnipay.thephpleague.com/api/recurring-billing/
     * 
     * @param null|array $value
     * 
     * If not null, should be an array with the following keys:
     * 
     * 'vads_sub_amount'                n..12       Montant des échéances de l’abonnement pour toutes les échéances, hormis celles éventuellement définies par vads_sub_init_amount_number
     * 'vads_sub_currency'              n3          Code numérique de la monnaie à utiliser pour l’abonnement, selon la norme ISO 4217.
     * 'vads_sub_desc'                  ans...255   Règle de récurrence à appliquer suivant la spécification iCalendar RFC5545.
     * 'vads_sub_effect_date'           n8          Date d'effet de l'abonnement. 
     * 'vads_sub_init_amount'           n..12       Montant des échéances de l’abonnement pour les premières échéances.
     * 'vads_sub_init_amount_number'    n..3        Nombre d’échéances auxquelles il faudra appliquer le montant vads_sub_init_amount
     * 'vads_subscription'              ans..50     Identifiant de l'abonnement à créer.
     * 
     * @return AbstractRequest
     */
    public function setToken($value){
        return $this->setParameter('token', $value);
    }

    public function getToken(){
        return $this->getParameter('token');
    }


    /**
     * @see https://paiement.systempay.fr/doc/fr-FR/m-payment/webview/calculer-la-signature.html
     */
    protected function generateSignature($data)
    {
        // Sort the data
        ksort($data);

        // Filter only the vads_* fields
        $matchedKeys = array_filter(array_keys($data), function ($v) {
            return strpos($v, 'vads_') === 0;
        });
        $data = array_intersect_key($data, array_flip($matchedKeys));

        // Add the certificate
        $data[] = $this->getCertificate();

        return base64_encode(hash_hmac('sha256', implode('+', $data), $this->getCertificate(), true));
    }
}
