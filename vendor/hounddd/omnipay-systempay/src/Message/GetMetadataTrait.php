<?php

namespace Omnipay\SystemPay\Message;

trait GetMetadataTrait
{
    /**
     * @return array
     */
    public function getMetadata()
    {
        $prefix = 'vads_ext_info_';
        $metadata = array();

        foreach ($this->data as $key => $value) {
            if (0 === strpos($key, $prefix)) {
                $metadata[substr($key, strlen($prefix))] = $value;
            }
        }

        return $metadata;
    }
}
