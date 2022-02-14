<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Message\AbstractResponse;

abstract class AbstractResults extends AbstractResponse
{
    public function verifySignature()
    {
        return $this;
    }

    public function isSuccessful()
    {
        return true;
    }
}