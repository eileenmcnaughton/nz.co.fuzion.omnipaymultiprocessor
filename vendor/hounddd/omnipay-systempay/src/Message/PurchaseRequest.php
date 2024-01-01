<?php

namespace Omnipay\SystemPay\Message;

/**
 * SystemPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{

    public $liveEndpoint = 'https://paiement.systempay.fr/vads-payment/';


    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     *
     */
    public function getData()
    {
        $this->validate('amount');

        $data = array();
        $data['vads_site_id'] = $this->getMerchantId();
        $data['vads_ctx_mode'] = $this->getTestMode() ? 'TEST' : 'PRODUCTION';
        $data['vads_trans_id'] = str_pad($this->getTransactionId(), 6, '0', STR_PAD_LEFT);
        $data['vads_trans_date'] = $this->getTransactionDate() ? $this->getTransactionDate() : date('YmdHis');
        $data['vads_amount'] = $this->getAmountInteger();
        $data['vads_currency'] = $this->getCurrencyNumeric();
        $data['vads_action_mode'] = 'INTERACTIVE';
        $data['vads_page_action'] = 'PAYMENT';
        $data['vads_version'] = 'V2';
        $data['vads_payment_config'] = $this->getPaymentConfig();
        $data['vads_capture_delay'] = 0;
        $data['vads_validation_mode'] = 0;
        $data['vads_url_success'] = $this->getSuccessUrl();
        $data['vads_url_cancel'] = $this->getCancelUrl();
        $data['vads_url_error'] = $this->getErrorUrl();
        $data['vads_url_refused'] = $this->getRefusedUrl();
        $data['vads_order_id'] = $this->getOrderId();
        $data['vads_payment_cards'] = $this->getPaymentCards();

        if (null !== $this->getNotifyUrl()) {
            $data['vads_url_check'] = $this->getNotifyUrl();
        }

        $subscription = $this->getToken();
        if($subscription){
            foreach($subscription as $key=>$value){
                $data[$key] = $value;
            }
            $data['vads_sub_currency'] = $this->getCurrencyNumeric();
            // $data['vads_url_check_src'] = '';
        }

        // Customer infos
        if ($this->getCard()) {
            $data['vads_cust_first_name'] = $this->getCard()->getName();
            $data['vads_cust_address'] = $this->getCard()->getAddress1();
            $data['vads_cust_city'] = $this->getCard()->getCity();
            $data['vads_cust_state'] = $this->getCard()->getState();
            $data['vads_cust_zip'] = $this->getCard()->getPostcode();
            $data['vads_cust_country'] = $this->getCard()->getCountry();
            $data['vads_cust_phone'] = $this->getCard()->getPhone();
            $data['vads_cust_email'] = $this->getCard()->getEmail();
        }

        $metadata = $this->getMetadata();
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $data['vads_ext_info_' . $key] = $value;
            }
        }

        $data['signature'] = $this->generateSignature($data);

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }


    public function getEndpoint()
    {
        return $this->liveEndpoint;
    }
}
