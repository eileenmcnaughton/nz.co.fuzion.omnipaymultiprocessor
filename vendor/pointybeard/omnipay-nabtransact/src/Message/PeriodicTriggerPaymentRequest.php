<?php

/**
 *
 */
namespace Omnipay\NABTransact\Message;

/**
 * 
 */
final class PeriodicTriggerPaymentRequest extends PeriodicAbstractRequest
{
    public static $RESPONSE_CODES = [
        '00' => 'Approved',
        '01' => 'Refer to Card Issuer ',
        '02' => 'Refer to Issuer’s Special Conditions',
        '03' => 'Invalid Merchant',
        '04' => 'Pick Up Card',
        '05' => 'Do Not Honour',
        '06' => 'Error',
        '07' => 'Pick Up Card, Special Conditions',
        '08' => 'Approved',
        '09' => 'Request in Progress',
        '10' => 'Partial Amount Approved',
        '11' => 'Approved (not used)',
        '12' => 'Invalid Transaction',
        '13' => 'Invalid Amount',
        '14' => 'Invalid Card Number',
        '15' => 'No Such Issuer',
        '16' => 'Approved (not used)',
        '17' => 'Customer Cancellation',
        '18' => 'Customer Dispute',
        '19' => 'Re-enter Transaction',
        '20' => 'Invalid Response',
        '21' => 'No Action Taken',
        '22' => 'Suspected Malfunction',
        '23' => 'Unacceptable Transaction Fee',
        '24' => 'File Update not Supported by Receiver',
        '25' => 'Unable to Locate Record on File',
        '26' => 'Duplicate File Update Record',
        '27' => 'File Update Field Edit Error',
        '28' => 'File Update File Locked Out',
        '29' => 'File Update not Successful',
        '30' => 'Format Error',
        '31' => 'Bank not Supported by Switch',
        '32' => 'Completed Partially',
        '33' => 'Expired Card—Pick Up',
        '34' => 'Suspected Fraud—Pick Up',
        '35' => 'Contact Acquirer—Pick Up',
        '36' => 'Restricted Card—Pick Up',
        '37' => 'Call Acquirer Security—Pick Up',
        '38' => 'Allowable PIN Tries Exceeded',
        '39' => 'No CREDIT Account',
        '40' => 'Requested Function not Supported',
        '41' => 'Lost Card—Pick Up',
        '42' => 'No Universal Amount',
        '43' => 'Stolen Card—Pick Up',
        '44' => 'No Investment Account',
        '51' => 'Insufficient Funds',
        '52' => 'No Cheque Account',
        '53' => 'No Savings Account',
        '54' => 'Expired Card',
        '55' => 'Incorrect PIN',
        '56' => 'No Card Record',
        '57' => 'Trans. not Permitted to Cardholder',
        '58' => 'Transaction not Permitted to Terminal',
        '59' => 'Suspected Fraud',
        '60' => 'Card Acceptor Contact Acquirer',
        '61' => 'Exceeds Withdrawal Amount Limits',
        '62' => 'Restricted Card',
        '63' => 'Security Violation',
        '64' => 'Original Amount Incorrect',
        '65' => 'Exceeds Withdrawal Frequency Limit',
        '66' => 'Card Acceptor Call Acquirer Security',
        '67' => 'Hard Capture—Pick Up Card at ATM',
        '68' => 'Response Received Too Late',
        '75' => 'Allowable PIN Tries Exceeded',
        '86' => 'ATM Malfunction',
        '87' => 'No Envelope Inserted',
        '88' => 'Unable to Dispense',
        '89' => 'Administration Error',
        '90' => 'Cut-off in Progress',
        '91' => 'Issuer or Switch is Inoperative',
        '92' => 'Financial Institution not Found',
        '93' => 'Trans Cannot be Completed',
        '94' => 'Duplicate Transmission',
        '95' => 'Reconcile Error',
        '96' => 'System Malfunction',
        '97' => 'Reconciliation Totals Reset',
        '98' => 'MAC Error',
        '99' => 'Reserved for National Use',
    ];

    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
    }

    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    public function setTransactionAmount($value)
    {
        return $this->setParameter('transactionAmount', $value);
    }

    public function getTransactionAmount()
    {
        return $this->getParameter('transactionAmount');
    }

    public function setTransactionCurrency($value)
    {
        return $this->setParameter('transactionCurrency', $value);
    }

    public function getTransactionCurrency()
    {
        return $this->getParameter('transactionCurrency');
    }

    public function getData()
    {
        $data = $this->getBaseData();
        $this->validate(['customerReference', 'transactionReference', 'transactionAmount', 'transactionCurrency']);
        $data['Transaction'] = [
            'Amount' => $this->getTransactionAmount(),
            'Currency' => $this->getTransactionCurrency(),
            'Reference' => $this->getTransactionReference(),
        ];

        return $data;
    }

    public function validate()
    {
		$parameterNames = func_get_args()[0];

        foreach ($parameterNames as $parameter) {
            parent::validate($parameter);
        }
    }

    public function getActionType()
    {
        return 'trigger';
    }

    protected function buildRequestBody(array $data)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<NABTransactMessage>
    <MessageInfo>
        <messageID>'.$data['MessageID'].'</messageID>
        <messageTimestamp>'.$data['MessageTimestamp'].'</messageTimestamp>
        <timeoutValue>60</timeoutValue>
        <apiVersion>'.$data['ApiVersion'].'</apiVersion>
    </MessageInfo>
    <MerchantInfo>
        <merchantID>'.$data['Credentials']['MerchantID'].'</merchantID>
        <password>'.$data['Credentials']['Password'].'</password>
    </MerchantInfo>
    <RequestType>Periodic</RequestType>
    <Periodic>
        <PeriodicList count="1">
            <PeriodicItem ID="1">
                <actionType>'.$data['ActionType'].'</actionType>
                <periodicType>8</periodicType>
                <crn>'.$data['CustomerReferenceNumber'].'</crn>
                <transactionReference>'.$data['Transaction']['Reference'].'</transactionReference>
                <amount>'.$data['Transaction']['Amount'].'</amount>
                <currency>'.$data['Transaction']['Currency'].'</currency>
                <CreditCardInfo>
                    <recurringFlag>no</recurringFlag>
                </CreditCardInfo>
            </PeriodicItem>
        </PeriodicList>
    </Periodic>
</NABTransactMessage>';
    }
}
