<?php
/**
 * eWAY Rapid Direct Create Card Response
 */

namespace Omnipay\Eway\Message;

use Omnipay\SecurePay\Message\DirectPostCompletePurchaseResponse;

/**
 * eWAY Rapid Direct Create Card Response
 *
 * This is the response class for Rapid Direct when creating
 * or updating a card
 *
 */
class RapidDirectCreateCardResponse extends RapidResponse
{
  /**
   * @var DirectPostCompletePurchaseResponse
   */
    protected $purchaseResponse;

    /**
     * @return DirectPostCompletePurchaseResponse
     */
    public function getPurchaseResponse()
    {
        return $this->purchaseResponse;
    }

    /**
     * @param DirectPostCompletePurchaseResponse $purchaseResponse
     */
    public function setPurchaseResponse($purchaseResponse)
    {
        $this->purchaseResponse = $purchaseResponse;
    }

    public function isSuccessful()
    {
        if (!$this->getPurchaseResponse()) {
            return $this->data['ResponseMessage'] == 'A2000';
        } else {
            return ($this->data['ResponseMessage'] == 'A2000' && $this->purchaseResponse->isSuccessful());
        }
    }
}
