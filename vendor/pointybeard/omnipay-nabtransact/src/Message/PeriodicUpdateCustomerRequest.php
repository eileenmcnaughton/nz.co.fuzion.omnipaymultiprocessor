<?php

/**
 *
 */
namespace Omnipay\NABTransact\Message;

/**
 * 
 */
final class PeriodicUpdateCustomerRequest extends PeriodicAbstractRequest
{
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    public function getData()
    {
        $data = $this->getBaseData();
        $this->validate();

        return $data;
    }

    public function validate()
    {
        parent::validate('card');
        parent::validate('customerReference');
    }

    public function getActionType()
    {
        return 'editcrn';
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
                <periodicType>5</periodicType>
                <crn>'.$data['CustomerReferenceNumber'].'</crn>
                <CreditCardInfo>
                    <cardNumber>'.$data['Customer']['CardDetails']['Number'].'</cardNumber>
                    <expiryDate>'.$data['Customer']['CardDetails']['ExpiryMonth'].'/'.$data['Customer']['CardDetails']['ExpiryYear'].'</expiryDate>
                    <cvv>'.$data['Customer']['CardDetails']['Cvv'].'</cvv>
                </CreditCardInfo>
            </PeriodicItem>
        </PeriodicList>
    </Periodic>
</NABTransactMessage>';
    }
}
