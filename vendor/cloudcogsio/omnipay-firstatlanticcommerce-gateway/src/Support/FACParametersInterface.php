<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

interface FACParametersInterface
{
    public function setFacId($FACID);
    public function getFacId();
    public function setFacPwd($PWD);
    public function getFacPwd();
    public function setFacAcquirer($ACQ);
    public function getFacAcquirer();
}