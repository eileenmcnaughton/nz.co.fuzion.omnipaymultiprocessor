<?php
/**
 * eWAY Rapid Direct Create Card Response
 */
 
namespace Omnipay\Eway\Message;

/**
 * eWAY Rapid Direct Create Card Response
 * 
 * This is the response class for Rapid Direct when creating 
 * or updating a card
 *
 */
class RapidDirectCreateCardResponse extends RapidResponse
{
    public function isSuccessful()
    {
        return $this->data['ResponseMessage'] == 'A2000';
    }
}
