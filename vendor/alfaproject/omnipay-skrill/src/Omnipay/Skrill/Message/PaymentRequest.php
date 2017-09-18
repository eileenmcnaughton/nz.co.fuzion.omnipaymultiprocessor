<?php
namespace Omnipay\Skrill\Message;

use DateTime;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Skrill Payment Request
 *
 * Once the customer has reached the merchant's checkout/cashier page, they should be
 * presented with a button which posts a HTML form to Skrill payment endpoint.
 *
 * Sometimes the merchant may wish to keep the details of the payment secret. This is the
 * case when the parameters submitted to the Skrill Servers contain sensitive information
 * that should not be altered by the customer. When using the standard procedure for
 * redirecting the customer, he is able to see and possible modify the payment parameters
 * since their browser performs the actual request for the transaction.
 *
 * This class allows Skrill to prepare a session for the payment. We then use this
 * session details to redirect the customer without sharing any payment information,
 * where the normal flow of events continues. This redirect must happen within 15 minutes
 * of the original request otherwise the session will expire.
 *
 * This way the details of the payment are communicated securely only between the
 * merchant's server and Skrill.
 *
 * @author    Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   6.5 Skrill Payment Gateway Integration Guide
 */
class PaymentRequest extends AbstractRequest
{
    /**
     * Get the email address of the merchant's Skrill account.
     *
     * @return string email
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Set the email address of the merchant's Skrill account.
     *
     * @param string $value email
     *
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the description to be shown on the Skrill Gateway page.
     *
     * @return string recipient description
     */
    public function getRecipientDescription()
    {
        return $this->getParameter('recipientDescription');
    }

    /**
     * Set the description to be shown on the Skrill Gateway page.
     *
     * If no value is set, the merchant's email is shown as the recipient of the payment.
     * (Max. 30 characters)
     *
     * @param string $value recipient description
     *
     * @return $this
     */
    public function setRecipientDescription($value)
    {
        return $this->setParameter('recipientDescription', $value);
    }

    /**
     * Get the URL to which the customer is returned once the payment is made.
     *
     * @return string return url
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * Set the URL to which the customer is returned once the payment is made.
     *
     * If this field is not filled, the Skrill Gateway page closes automatically at the
     * end of the transaction and the customer is returned to the page on the merchant's
     * website from where they were redirected to Skrill.
     *
     * @param string $value return url
     *
     * @return $this|\Omnipay\Common\Message\AbstractRequest
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * Get the text on the button when the customer finishes their payment.
     *
     * @return string return url text
     */
    public function getReturnUrlText()
    {
        return $this->getParameter('returnUrlText');
    }

    /**
     * Set the text on the button when the customer finishes their payment.
     *
     * @param string $value return url text
     *
     * @return $this
     */
    public function setReturnUrlText($value)
    {
        return $this->setParameter('returnUrlText', $value);
    }

    /**
     * Get the target in which the return url value is displayed upon successful payment
     * from the customer.
     *
     * @return int return url target
     */
    public function getReturnUrlTarget()
    {
        return $this->getParameter('returnUrlTarget');
    }

    /**
     * Set the target in which the return url value is displayed upon successful payment
     * from the customer.
     *
     * Default value is 1.
     *
     * * 1 = _top
     * * 2 = _parent
     * * 3 = _self
     * * 4 = _blank
     *
     * @param int $value return url target
     *
     * @return $this
     */
    public function setReturnUrlTarget($value)
    {
        switch ($value) {
            case '_top':
                $value = 1;
                break;

            case '_parent':
                $value = 2;
                break;

            case '_self':
                $value = 3;
                break;

            case '_blank':
                $value = 4;
                break;

            default:
                $value = (int)$value;
        }

        return $this->setParameter('returnUrlTarget', $value);
    }

    /**
     * Get the URL to which the customer is returned if the payment is cancelled.
     *
     * @return string cancel url
     */
    public function getCancelUrl()
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * Set the URL to which the customer is returned if the payment is cancelled.
     *
     * If this field is not filled, the Skrill Gateway page closes automatically when the
     * Cancel button is selected, and customer is returned to the page on the merchant's
     * site from where they redirected to Skrill.
     *
     * @param string $value cancel url
     *
     * @return $this|\Omnipay\Common\Message\AbstractRequest
     */
    public function setCancelUrl($value)
    {
        return $this->setParameter('cancelUrl', $value);
    }

    /**
     * Get the target in which the cancel url value is displayed upon cancellation of
     * payment by the customer.
     *
     * @return int cancel url target
     */
    public function getCancelUrlTarget()
    {
        return $this->getParameter('cancelUrlTarget');
    }

    /**
     * Set the target in which the cancel url value is displayed upon cancellation of
     * payment by the customer.
     *
     * Default value is 1.
     *
     * * 1 = _top
     * * 2 = _parent
     * * 3 = _self
     * * 4 = _blank
     *
     * @param int $value cancel url target
     *
     * @return $this
     */
    public function setCancelUrlTarget($value)
    {
        switch ($value) {
            case '_top':
                $value = 1;
                break;

            case '_parent':
                $value = 2;
                break;

            case '_self':
                $value = 3;
                break;

            case '_blank':
                $value = 4;
                break;

            default:
                $value = (int)$value;
        }

        return $this->setParameter('cancelUrlTarget', $value);
    }

    /**
     * Get the second URL to which the transaction details are posted after the payment
     * process is complete.
     *
     * @return string notify url 2
     */
    public function getNotifyUrl2()
    {
        return $this->getParameter('notifyUrl2');
    }

    /**
     * Get the second URL to which the transaction details are posted after the payment
     * process is complete.
     *
     * Alternatively, you may specify an email address where the results are sent.
     *
     * @param string $value notify url 2
     *
     * @return $this
     */
    public function setNotifyUrl2($value)
    {
        return $this->setParameter('notifyUrl2', $value);
    }

    /**
     * Get whether the gateway redirects customers to a new window instead of in the
     * same browser window.
     *
     * @return bool new window redirect
     */
    public function getNewWindowRedirect()
    {
        return $this->getParameter('newWindowRedirect');
    }

    /**
     * Set whether the gateway redirects customers to a new window instead of in the
     * same browser window.
     *
     * e.g., for online bank transfer payment methods, such as Sofortueberweisung.
     *
     * @param bool $value new window redirect
     *
     * @return $this
     */
    public function setNewWindowRedirect($value)
    {
        return $this->setParameter('newWindowRedirect', (bool)$value);
    }

    /**
     * Get the 2-letter code of the language used for Skrill's pages.
     *
     * @return string language
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * Set the 2-letter code of the language used for Skrill's pages.
     *
     * Can be any of EN, DE, ES, FR, IT, PL, GR, RO, RU, TR, CN, CZ, NL, DA, SV or FI.
     *
     * @param string $value email
     *
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * Get whether the merchant show their customers the gateway page without the
     * prominent login section.
     *
     * @deprecated On the new payment page, the login fields are hidden by default.
     * @return bool hide login
     */
    public function getHideLogin()
    {
        return $this->getParameter('hideLogin');
    }

    /**
     * Set whether the merchant show their customers the gateway page without the
     * prominent login section.
     *
     * @deprecated On the new payment page, the login fields are hidden by default.
     *
     * @param  bool $value hide login
     *
     * @return self
     */
    public function setHideLogin($value)
    {
        return $this->setParameter('hideLogin', (bool)$value);
    }

    /**
     * Get the confirmation message or other details at the end of the payment process.
     *
     * @return string confirmation note
     */
    public function getConfirmationNote()
    {
        return $this->getParameter('confirmationNote');
    }

    /**
     * Set the confirmation message or other details at the end of the payment process.
     *
     * Line breaks &lt;br&gt; may be used for longer messages.
     *
     * @param string $value confirmation note
     *
     * @return $this
     */
    public function setConfirmationNote($value)
    {
        return $this->setParameter('confirmationNote', $value);
    }

    /**
     * Get the URL of the logo which you would like to appear at the top of the Skrill
     * page.
     *
     * @return string logo url
     */
    public function getLogoUrl()
    {
        return $this->getParameter('logoUrl');
    }

    /**
     * Set the URL of the logo which you would like to appear at the top of the Skrill
     * page.
     *
     * The logo must be accessible via HTTPS or it will not be shown. For best results
     * use logos with dimensions up to 200px in width and 50px in height.
     *
     * @param string $value logo url
     *
     * @return $this
     */
    public function setLogoUrl($value)
    {
        return $this->setParameter('logoUrl', $value);
    }

    /**
     * Get the unique referral ID or email of an affiliate from which the customer is
     * referred.
     *
     * @return string referral id
     */
    public function getReferralId()
    {
        return $this->getParameter('referralId');
    }

    /**
     * Set the unique referral ID or email of an affiliate from which the customer is
     * referred.
     *
     * The referral ID value must be included within the actual payment request.
     *
     * @param string $value referral id
     *
     * @return $this
     */
    public function setReferralId($value)
    {
        return $this->setParameter('referralId', $value);
    }

    /**
     * Get the additional identifier that the merchant can use in order to track
     * affiliates.
     *
     * @return string ext. referral id
     */
    public function getExtReferralId()
    {
        return $this->getParameter('extReferralId');
    }

    /**
     * Set the additional identifier that the merchant can use in order to track
     * affiliates.
     *
     * You MUST inform your account manager about the exact value that will be submitted
     * so that affiliates can be tracked.
     *
     * @param string $value ext. referral id
     *
     * @return $this
     */
    public function setExtReferralId($value)
    {
        return $this->setParameter('extReferralId', $value);
    }

    /**
     * Get the list of fields that should be passed back to the merchant's server when
     * the payment is confirmed.
     *
     * @return array merchant fields
     */
    public function getMerchantFields()
    {
        return $this->getParameter('merchantFields');
    }

    /**
     * Get the list of fields that should be passed back to the merchant's server when
     * the payment is confirmed.
     *
     * Maximum of 5 fields.
     *
     * @param array $value merchant fields
     *
     * @return $this
     */
    public function setMerchantFields(array $value)
    {
        return $this->setParameter('merchantFields', $value);
    }

    /**
     * Get the email address of the customer who is making the payment.
     *
     * @return string customer's email
     */
    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    /**
     * Set the email address of the customer who is making the payment.
     *
     * If left empty, the customer has to enter their email address.
     *
     * @param string $value customer's email
     *
     * @return $this
     */
    public function setCustomerEmail($value)
    {
        return $this->setParameter('customerEmail', $value);
    }

    /**
     * Get the customer's title.
     *
     * @return string customer's title
     */
    public function getCustomerTitle()
    {
        return $this->getParameter('customerTitle');
    }

    /**
     * Set the customer's title.
     *
     * Accepted values: Mr, Mrs or Ms.
     *
     * @param string $value customer's title
     *
     * @return $this
     */
    public function setCustomerTitle($value)
    {
        return $this->setParameter('customerTitle', $value);
    }

    /**
     * Get the customer's first name.
     *
     * @return string customer's first name
     */
    public function getCustomerFirstName()
    {
        return $this->getParameter('customerFirstName');
    }

    /**
     * Set the customer's first name.
     *
     * @param string $value customer's first name
     *
     * @return $this
     */
    public function setCustomerFirstName($value)
    {
        return $this->setParameter('customerFirstName', $value);
    }

    /**
     * Get the customer's last name.
     *
     * @return string customer's last name
     */
    public function getCustomerLastName()
    {
        return $this->getParameter('customerLastName');
    }

    /**
     * Set the customer's last name.
     *
     * @param string $value customer's last name
     *
     * @return $this
     */
    public function setCustomerLastName($value)
    {
        return $this->setParameter('customerLastName', $value);
    }

    /**
     * Get the date of birth of the customer.
     *
     * @return DateTime|null customer's birthday
     */
    public function getCustomerBirthday()
    {
        return $this->getParameter('customerBirthday');
    }

    /**
     * Set the date of birth of the customer.
     *
     * @param DateTime|null $value customer's birthday
     *
     * @return $this
     */
    public function setCustomerBirthday(DateTime $value = null)
    {
        return $this->setParameter('customerBirthday', $value);
    }

    /**
     * Get the customer's address. (e.g. street)
     *
     * @return string customer's address
     */
    public function getCustomerAddress1()
    {
        return $this->getParameter('customerAddress1');
    }

    /**
     * Set the customer's address. (e.g. street)
     *
     * @param string $value customer's address
     *
     * @return $this
     */
    public function setCustomerAddress1($value)
    {
        return $this->setParameter('customerAddress1', $value);
    }

    /**
     * Get the customer's address. (e.g. town)
     *
     * @return string customer's address
     */
    public function getCustomerAddress2()
    {
        return $this->getParameter('customerAddress2');
    }

    /**
     * Set the customer's address. (e.g. town)
     *
     * @param string $value customer's address
     *
     * @return $this
     */
    public function setCustomerAddress2($value)
    {
        return $this->setParameter('customerAddress2', $value);
    }

    /**
     * Get the customer's phone number.
     *
     * @return string customer's phone
     */
    public function getCustomerPhone()
    {
        return $this->getParameter('customerPhone');
    }

    /**
     * Set the customer's phone number.
     *
     * Only numeric values are accepted.
     *
     * @param string $value customer's phone
     *
     * @return $this
     */
    public function setCustomerPhone($value)
    {
        return $this->setParameter('customerPhone', $value);
    }

    /**
     * Get the customer's postal code or ZIP Code.
     *
     * @return string customer's postal code
     */
    public function getCustomerPostalCode()
    {
        return $this->getParameter('customerPostalCode');
    }

    /**
     * Set the customer's postal code or ZIP Code.
     *
     * Only alphanumeric values are accepted. (e.g., no punctuation marks or dashes)
     *
     * @param string $value customer's postal code
     *
     * @return $this
     */
    public function setCustomerPostalCode($value)
    {
        return $this->setParameter('customerPostalCode', $value);
    }

    /**
     * Get the customer's city.
     *
     * @return string customer's city
     */
    public function getCustomerCity()
    {
        return $this->getParameter('customerCity');
    }

    /**
     * Set the customer's city.
     *
     * @param string $value customer's city
     *
     * @return $this
     */
    public function setCustomerCity($value)
    {
        return $this->setParameter('customerCity', $value);
    }

    /**
     * Get the customer's state or region.
     *
     * @return string customer's state or region
     */
    public function getCustomerState()
    {
        return $this->getParameter('customerState');
    }

    /**
     * Set the customer's state or region.
     *
     * @param string $value customer's state or region
     *
     * @return $this
     */
    public function setCustomerState($value)
    {
        return $this->setParameter('customerState', $value);
    }

    /**
     * Get the customer's country in the 3-digit ISO Code.
     *
     * @return string customer's country
     */
    public function getCustomerCountry()
    {
        return $this->getParameter('customerCountry');
    }

    /**
     * Set the customer's country in the 3-digit ISO Code.
     *
     * @param string $value customer's country
     *
     * @return $this
     */
    public function setCustomerCountry($value)
    {
        return $this->setParameter('customerCountry', $value);
    }

    /**
     * Get the detailed calculations for the total amount payable.
     *
     * The amount descriptions are an associative array, where the keys are descriptions
     * and values are the amounts.
     *
     * @return array amount descriptions
     */
    public function getAmountDescriptions()
    {
        return $this->getParameter('amountDescriptions');
    }

    /**
     * Set the detailed calculations for the total amount payable.
     *
     * The amount descriptions are an associative array, where the keys are descriptions
     * and values are the amounts.
     * Please note that Skrill does not check the validity of these data - they are only
     * displayed in the 'More information' section in the header of the gateway.
     * These amounts are in the currency defined in the currency field and will be shown
     * next to the descriptions.
     *
     * @param array $value amount descriptions
     *
     * @return $this
     */
    public function setAmountDescriptions(array $value)
    {
        return $this->setParameter('amountDescriptions', $value);
    }

    /**
     * Get the transfer details that show in the 'More information' section in the header
     * of the gateway.
     *
     * The details are an associative array, where the keys are descriptions and values
     * are the texts.
     *
     * @return array details
     */
    public function getDetails()
    {
        return $this->getParameter('details');
    }

    /**
     * Set the transfer details that show in the 'More information' section in the header
     * of the gateway.
     *
     * The details are an associative array, where the keys are descriptions and values
     * are the texts.
     * These texts are also shown to the client in his history at Skrill's website.
     *
     * @param array $value details
     *
     * @return $this
     */
    public function setDetails(array $value)
    {
        return $this->setParameter('details', $value);
    }

    /**
     * Get the list of payment method codes, indicating the payment methods to be
     * presented to the customer.
     *
     * @return array payment methods
     */
    public function getPaymentMethods()
    {
        return $this->getParameter('paymentMethods');
    }

    /**
     * Set the list of payment method codes, indicating the payment methods to be
     * presented to the customer.
     *
     * @param  array $value payment methods
     *
     * @return self
     */
    public function setPaymentMethods(array $value)
    {
        return $this->setParameter('paymentMethods', $value);
    }

    /**
     * Set a payment method code, indicating the payment method to be presented to the
     * customer.
     *
     * Warning: this resets any previously set payment methods.
     *
     * @param  string $value payment method
     *
     * @return self
     */
    public function setPaymentMethod($value)
    {
        return $this->setPaymentMethods([$value]);
    }

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        // make sure we have the mandatory fields
        $this->validate('email', 'language', 'amount', 'currency', 'details');

        // merchant details
        $data['pay_to_email'] = $this->getEmail();
        $data['language'] = $this->getLanguage();
        $data['recipient_description'] = $this->getRecipientDescription();
        $data['transaction_id'] = $this->getTransactionId();
        $data['return_url'] = $this->getReturnUrl();
        $data['return_url_text'] = $this->getReturnUrlText();
        $data['return_url_target'] = $this->getReturnUrlTarget();
        $data['cancel_url'] = $this->getCancelUrl();
        $data['cancel_url_target'] = $this->getCancelUrlTarget();
        $data['status_url'] = $this->getNotifyUrl();
        $data['status_url2'] = $this->getNotifyUrl2();
        $data['new_window_redirect'] = $this->getNewWindowRedirect() ? 1 : 0;
        $data['hide_login'] = $this->getHideLogin() ? 1 : 0;
        $data['confirmation_note'] = $this->getConfirmationNote();
        $data['logo_url'] = $this->getLogoUrl();
        $data['prepare_only'] = 1;
        $data['rid'] = $this->getReferralId();
        $data['ext_ref_id'] = $this->getExtReferralId();

        $merchantFields = $this->getMerchantFields();
        if (is_array($merchantFields)) {
            $data['merchant_fields'] = implode(',', array_keys($merchantFields));
            foreach ($merchantFields as $field => $value) {
                $data[$field] = $value;
            }
        }

        // customer details
        $data['pay_from_email'] = $this->getCustomerEmail();
        $data['title'] = $this->getCustomerTitle();
        $data['firstname'] = $this->getCustomerFirstName();
        $data['lastname'] = $this->getCustomerLastName();

        $customerBirthday = $this->getCustomerBirthday();
        if ($customerBirthday) {
            $data['date_of_birth'] = $customerBirthday->format('dmY');
        }

        $data['address'] = $this->getCustomerAddress1();
        $data['address2'] = $this->getCustomerAddress2();
        $data['phone_number'] = $this->getCustomerPhone();
        $data['postal_code'] = $this->getCustomerPostalCode();
        $data['city'] = $this->getCustomerCity();
        $data['state'] = $this->getCustomerState();
        $data['country'] = $this->getCustomerCountry();

        // payment details
        $data['amount'] = rtrim(rtrim($this->getAmount(), '0'), '.');
        $data['currency'] = $this->getCurrency();

        $amountDescriptions = $this->getAmountDescriptions();
        if (is_array($amountDescriptions)) {
            $counter = 2;
            foreach ($amountDescriptions as $description => $amount) {
                $data['amount' . $counter . '_description'] = $description;
                $data['amount' . $counter] = $amount;
                $counter++;
            }
        }

        $details = $this->getDetails();
        $counter = 1;
        foreach ($details as $description => $text) {
            $data['detail' . $counter . '_description'] = $description;
            $data['detail' . $counter . '_text'] = $text;
            $counter++;
        }

        // split gateway
        $paymentMethods = $this->getPaymentMethods();
        if (is_array($paymentMethods)) {
            $data['payment_methods'] = implode(',', $paymentMethods);
        }

        return $data;
    }

    /**
     * @param  array $data payment data to send
     *
     * @return PaymentResponse         payment response
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();

        return $this->response = new PaymentResponse($this, $httpResponse);
    }

    /**
     * Get the endpoint for this request.
     *
     * @return string endpoint
     */
    public function getEndpoint()
    {
        return 'https://pay.skrill.com';
    }
}
