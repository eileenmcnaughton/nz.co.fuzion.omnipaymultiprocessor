<?php
/**
 * eWAY Rapid Shared Page Create Card Request
 */

namespace Omnipay\Eway\Message;

/**
 * eWAY Rapid Shared Page Create Card Request
 *
 * Creates a payment URL using eWAY's Responsive Shared Page
 *
 * @link https://eway.io/api-v3/#responsive-shared-page
 */
class RapidSharedCreateCardRequest extends RapidSharedPurchaseRequest
{
    public function getData()
    {
        $this->validate('returnUrl');

        $data = $this->getBaseData();
        $data['Method'] = 'CreateTokenCustomer';
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

        return $data;
    }
}
