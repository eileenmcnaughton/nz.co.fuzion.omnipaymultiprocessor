<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

abstract class AbstractSupport
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    public function setData (array $data)
    {
        foreach ($data as $h=>$v)
        {
            if (array_key_exists($h, $this->data))
            {
                $this->data[$h] = $v;
            }
        }

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}