<?php

/**
 *
 */
namespace Omnipay\NABTransact\Message;

/**
 * 
 */
final class PeriodicCreateCustomerRequest extends PeriodicAbstractRequest
{
    protected static function generateCustomerReferenceNumber($length = 20)
    {
        //Format enforced by NAB API is:
        // characters 0-9, A-Z, a-z, space, &, -, period
        // MINLEN = 1, MAXLEN = 20
        $crn = hash('sha256', microtime());
        $crn = preg_replace('/[^0-9A-Za-z &-.]/', null, $crn);

        return substr($crn, 0, $length);
    }

    public function getCustomerReference()
    {
        if (null === $this->getParameter('customerReference')) {
            $this->setParameter('customerReference', self::generateCustomerReferenceNumber());
        }

        return $this->getParameter('customerReference');
    }

    public function getData()
    {
        $data = $this->getBaseData();
        $this->validate('card');

        return $data;
    }

    public function getActionType()
    {
        return 'addcrn';
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
