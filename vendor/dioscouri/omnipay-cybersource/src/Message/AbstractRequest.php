<?php
namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 *
 * @method \Omnipay\Cybersource\Message\Response send()
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://secureacceptance.cybersource.com/silent';
    protected $testEndpoint = 'https://testsecureacceptance.cybersource.com/silent';
    protected $endpoint = '';
    protected $isUsOrCanada = false;

    public function sendData($data)
    {
        return $this->response = new Response($this, $data, $this->getEndpoint());
    }

    public function getProfileId()
    {
        return $this->getParameter('profileId');
    }

    public function setProfileId($value)
    {
        return $this->setParameter('profileId', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getAccessKey()
    {
        return $this->getParameter('accessKey');
    }

    public function setAccessKey($value)
    {
        return $this->setParameter('accessKey', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getIsUsOrCanada()
    {
        return $this->getParameter('isUsOrCanada');
    }

    public function setIsUsOrCanada($value)
    {
        return $this->setParameter('isUsOrCanada', $value);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getCardTypes()
    {
        return array(
            'visa' => '001',
            'mastercard' => '002',
            'amex' => '003',
            'discover' => '004',
            'diners_club' => '005',
            'carte_blanche' => '006',
            'jcb' => '007',
            'jal' => '021',
            'maestro' => '024',
            'delta' => '031',
            'visa_electron' => '033',
            'dankort' => '034',
            'carte_bleue' => '036',
            'carta_si' => '037',
            'maestro_international' => '042',
            'ge_money_uk_card' => '043'
        );
    }

    public function getCardType()
    {
        $types = $this->getCardTypes();
        $brand = $this->getCard()->getBrand();
        return empty($types[$brand]) ? null : $types[$brand];
    }

    /**
     *
     * @param array $data
     * @param array $fields
     * @param string $secret_key
     *
     * @return string
     */
    public function generateSignature($data, $fields, $secret_key)
    {
        $data_to_sign = array();
        foreach ($fields as $field) {
            $data_to_sign[] = $field . "=" . $data[$field];
        }
        $pairs = implode(',', $data_to_sign);
        return base64_encode(hash_hmac('sha256', $pairs, $secret_key, true));
    }

    public function supportsDeleteCard()
    {
        return false;
    }
}
