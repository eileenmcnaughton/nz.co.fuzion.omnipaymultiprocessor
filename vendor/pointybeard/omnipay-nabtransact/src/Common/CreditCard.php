<?php

namespace Omnipay\NABTransact\Common;

final class CreditCard extends \Omnipay\Common\CreditCard
{
    /**
     * Returns a masked credit card number that matches the masked format
     * NAB returns. This method exists since the 'editcrn' action does not
     * return the masked PAN.
     *
     * @param string $mask Character to use in place of numbers
     *
     * @return string
     */
    public function getNumberMasked($mask = '.')
    {
        return $this->getNumberFirstSix().str_repeat($mask, 3).$this->getNumberLastThree();
    }

    public function getNumberLastThree($value = '')
    {
        return substr($this->getNumber(), -3, 3) ?: null;
    }

    public function getNumberFirstSix($value = '')
    {
        return substr($this->getNumber(), 0, 6) ?: null;
    }
}
