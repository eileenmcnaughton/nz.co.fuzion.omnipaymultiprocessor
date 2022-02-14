<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Exception\RequiredMessageFieldEmpty;
use Omnipay\FirstAtlanticCommerce\Support\USStates;
use Omnipay\FirstAtlanticCommerce\Support\TransactionCode;

class Authorize extends AbstractRequest
{
    const MESSAGE_PART_TRANSACTION_DETAILS = "TransactionDetails";
    const MESSAGE_PART_CARD_DETAILS = "CardDetails";
    const MESSAGE_PART_BILLING_DETAILS = "BillingDetails";
    const MESSAGE_PART_SHIPPING_DETAILS = "ShippingDetails";
    const MESSAGE_PART_3DS_DETAILS = "ThreeDSecureDetails";
    const MESSAGE_PART_RECURRING_DETAILS = "RecurringDetails";
    const MESSAHE_PART_FRAUD_DETAILS = "FraudDetails";

    const PARAM_SIGNATURE = 'Signature';
    const PARAM_ACQUIRERID = 'AcquirerId';
    const PARAM_MERCHANTID = 'MerchantId';
    const PARAM_ORDERNUMBER = 'OrderNumber';
    const PARAM_TRANSACTIONCODE = 'TransactionCode';
    const PARAM_AMOUNT = 'Amount';
    const PARAM_CURRENCY = 'Currency';
    const PARAM_CURRENCY_EXPONENT = 'CurrencyExponent';
    const PARAM_SIGNATURE_METHOD = 'SignatureMethod';
    const PARAM_IPADDRESS = 'IPAddress';
    const PARAM_CUSTOMER_REF = 'CustomerReference';

    const PARAM_CARD_NUMBER = "CardNumber";
    const PARAM_CARD_EXPIRY_DATE = "CardExpiryDate";
    const PARAM_CARD_CVV2 = "CardCVV2";
    const PARAM_CARD_ISSUE_NUMBER = "IssueNumber";
    const PARAM_CARD_START_DATE = "StartDate";

    const PARAM_BILLING_FIRSTNAME = "BillToFirstName";
    const PARAM_BILLING_LASTNAME = "BillToLastName";
    const PARAM_BILLING_ADDRESS1 = "BillToAddress";
    const PARAM_BILLING_ADDRESS2 = "BillToAddress2";
    const PARAM_BILLING_CITY = "BillToCity";
    const PARAM_BILLING_ZIP = "BillToZipPostCode";
    const PARAM_BILLING_STATE = "BillToState";
    const PARAM_BILLING_COUNTRY = "BillToCountry";
    const PARAM_BILLING_TELEPHONE = "BillToTelephone";
    const PARAM_BILLING_EMAIL = "BillToEmail";

    protected $TransactionDetailsRequirement = [
        self::PARAM_ACQUIRERID => ["R",0,11],
        self::PARAM_MERCHANTID => ["R",0,15],
        self::PARAM_ORDERNUMBER => ["R",0,150],
        self::PARAM_TRANSACTIONCODE => ["R",0,4],
        self::PARAM_AMOUNT => ["R",0,12],
        self::PARAM_CURRENCY => ["R",0,3],
        self::PARAM_CURRENCY_EXPONENT => ["R",0,1],
        self::PARAM_SIGNATURE_METHOD => ["R",0,4],
        self::PARAM_IPADDRESS => ["C",0,15],
        self::PARAM_CUSTOMER_REF => ["O",0,256]
    ];

    protected $CardDetailsRequirement = [
        self::PARAM_CARD_NUMBER => ["R",0,19],
        self::PARAM_CARD_EXPIRY_DATE => ["R",0,4],
        self::PARAM_CARD_CVV2 => ["R",0,4],
        self::PARAM_CARD_ISSUE_NUMBER => ["C",0,2],
        self::PARAM_CARD_START_DATE => ["C",0,4]
    ];

    protected $BillingDetailsRequirement = [
        self::PARAM_BILLING_FIRSTNAME => ["O",0,30],
        self::PARAM_BILLING_LASTNAME => ["O",0,30],
        self::PARAM_BILLING_ADDRESS1 => ["R",0,50],
        self::PARAM_BILLING_ADDRESS2 => ["O",0,50],
        self::PARAM_BILLING_CITY => ["O",0,30],
        self::PARAM_BILLING_STATE => ["O",0,5],
        self::PARAM_BILLING_ZIP => ["R",0,10],
        self::PARAM_BILLING_COUNTRY => ["O",0,3],
        self::PARAM_BILLING_TELEPHONE => ["O",0,20],
        self::PARAM_BILLING_EMAIL => ["O",0,50]
    ];

    protected $TransactionDetails = [];

    public function getData()
    {
        $this->TransactionDetails = array_merge($this->TransactionDetails, $this->setTransactionDetailsCommon());
        $this->setTransactionDetails();
        $this->setCardDetails();

        if ($this->getTransactionCode()->hasCode(TransactionCode::AVS_CHECK))
        {
            $this->setBillingDetails();
        }

        return $this->data;
    }

    protected function setTransactionDetails()
    {
        $this->TransactionDetails[self::PARAM_TRANSACTIONCODE] = $this->getTransactionCode();
        $this->TransactionDetails[self::PARAM_AMOUNT] = $this->getAmountForFAC();
        $this->TransactionDetails[self::PARAM_CURRENCY] = $this->getCurrencyNumeric();
        $this->TransactionDetails[self::PARAM_CURRENCY_EXPONENT] = $this->getCurrencyDecimalPlaces();
        $this->TransactionDetails[self::PARAM_SIGNATURE_METHOD] = $this->getSignatureMethod();
        $this->TransactionDetails[self::PARAM_IPADDRESS] = $this->getIPAddress();
        $this->TransactionDetails[self::PARAM_CUSTOMER_REF] = $this->getCustomerReference();

        $this->signTransaction();
        $this->TransactionDetails[self::PARAM_SIGNATURE] = $this->getSignature();

        $this->validateTransactionDetails();
    }

    protected function setTransactionDetailsCommon()
    {
        $container = [];
        $container[self::PARAM_ACQUIRERID] = $this->getFacAcquirer();
        $container[self::PARAM_MERCHANTID] = $this->getFacId();
        $container[self::PARAM_ORDERNUMBER] = $this->getTransactionId();

        return $container;
    }

    protected function validateTransactionDetails()
    {
        foreach ($this->TransactionDetailsRequirement as $param => $requirement)
        {
            $field = $this->TransactionDetails[$param];
            $this->TransactionDetails[$param] = substr($field, $requirement[1], $requirement[2]);

            switch ($requirement[0])
            {
                case "R":
                    if (empty($field)) throw new RequiredMessageFieldEmpty($param);

                    break;

                case "C":
                    if (empty($field)) $this->TransactionDetails[$param] = "";

                    break;

                default:
                    if (empty($field)) unset($this->TransactionDetails[$param]);
                    break;
            }

        }

        $this->data[self::MESSAGE_PART_TRANSACTION_DETAILS] = $this->TransactionDetails;
    }

    protected function setCardDetails()
    {
        $CardDetails = [];
        $CreditCard = $this->getCard();

        $CreditCard->validate();

        $CardDetails[self::PARAM_CARD_NUMBER] = $CreditCard->getNumber();
        $CardDetails[self::PARAM_CARD_EXPIRY_DATE] = $CreditCard->getExpiryDate("my");
        $CardDetails[self::PARAM_CARD_CVV2] = $CreditCard->getCvv();
        $CardDetails[self::PARAM_CARD_ISSUE_NUMBER] = $CreditCard->getIssueNumber();
        $CardDetails[self::PARAM_CARD_START_DATE] = $CreditCard->getStartDate("my");

        if ($CardDetails[self::PARAM_CARD_START_DATE] == "1299") unset($CardDetails[self::PARAM_CARD_START_DATE]);

        foreach ($this->CardDetailsRequirement as $param => $requirement)
        {
            $field = (isset($CardDetails[$param]))?$CardDetails[$param]:null;
            $CardDetails[$param] = substr($field, $requirement[1], $requirement[2]);

            switch ($requirement[0])
            {
                case "R":
                    if (empty($field)) throw new RequiredMessageFieldEmpty($param);

                    break;

                case "C":
                    if (empty($field)) $CardDetails[$param] = "";

                    break;

                default:
                    if (empty($field)) unset($CardDetails[$param]);
                    break;
            }
        }

        $this->data[self::MESSAGE_PART_CARD_DETAILS] = $CardDetails;
    }

    protected function setBillingDetails()
    {
        $BillingDetails = [];

        /* @var \Omnipay\FirstAtlanticCommerce\Support\CreditCard $CreditCard */
        $CreditCard = $this->getCard();

        $BillingCountry = $CreditCard->getBillingCountry();
        if ($BillingCountry != null)
        {
            $BillingDetails[self::PARAM_BILLING_COUNTRY] = $BillingCountry['numeric'];

            if ($BillingCountry['numeric'] == 840)
            {
                $States = new USStates();
                $State = strtoupper(substr($CreditCard->getBillingState(),0,2));
                if ($States->isValid($State))
                    $BillingDetails[self::PARAM_BILLING_STATE] = $State;
            }
        }

        $BillingDetails[self::PARAM_BILLING_FIRSTNAME] = $CreditCard->getBillingFirstName();
        $BillingDetails[self::PARAM_BILLING_LASTNAME] = $CreditCard->getBillingLastName();
        $BillingDetails[self::PARAM_BILLING_ADDRESS1] = $CreditCard->getBillingAddress1();
        $BillingDetails[self::PARAM_BILLING_ADDRESS2] = $CreditCard->getBillingAddress2();
        $BillingDetails[self::PARAM_BILLING_CITY] = $CreditCard->getBillingCity();
        $BillingDetails[self::PARAM_BILLING_ZIP] = $CreditCard->getBillingPostcode();
        $BillingDetails[self::PARAM_BILLING_TELEPHONE] = $CreditCard->getBillingPhone();
        $BillingDetails[self::PARAM_BILLING_EMAIL] = $CreditCard->getBillingEmail();

        foreach ($this->BillingDetailsRequirement as $param => $requirement)
        {
            $field = (isset($BillingDetails[$param]))?$BillingDetails[$param]:null;
            $BillingDetails[$param] = substr($field, $requirement[1], $requirement[2]);

            switch ($requirement[0])
            {
                case "R":
                    if (empty($field)) throw new RequiredMessageFieldEmpty($param);

                    break;

                case "C":
                    if (empty($field)) $BillingDetails[$param] = "";

                    break;

                default:
                    if (empty($field)) unset($BillingDetails[$param]);
                    break;
            }
        }

        $this->data[self::MESSAGE_PART_BILLING_DETAILS] = $BillingDetails;
    }

}