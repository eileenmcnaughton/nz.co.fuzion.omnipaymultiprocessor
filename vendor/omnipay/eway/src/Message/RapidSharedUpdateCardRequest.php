<?php
/**
 * eWAY Rapid Shared Page Update Card Request
 */

namespace Omnipay\Eway\Message;

/**
 * eWAY Rapid Shared Page Update Card Request
 *
 * Creates a payment URL using eWAY's Responsive Shared Page
 *
 * @link https://eway.io/api-v3/#responsive-shared-page
 */
class RapidSharedUpdateCardRequest extends RapidSharedPurchaseRequest
{
    public function getData()
    {
        $this->validate('returnUrl', 'cardReference');

        $data = $this->getBaseData();
        $data['Method'] = 'UpdateTokenCustomer';
        $data['TransactionType'] = 'Purchase';
        $data['RedirectUrl'] = $this->getReturnUrl();

        // Shared page parameters (optional)
        $data['CancelUrl'] = $this->getCancelUrl();
        $data['LogoUrl'] = $this->getLogoUrl();
        $data['HeaderText'] = $this->getHeaderText();
        $data['Language'] = $this->getLanguage();
        $data['CustomerReadOnly'] = $this->getCustomerReadOnly();
        $data['CustomView'] = $this->getCustomView();

        $data['Payment'] = array();
        $data['Payment']['TotalAmount'] = 0;

        $data['Customer']['TokenCustomerID'] = $this->getCardReference();

        return $data;
    }
}
