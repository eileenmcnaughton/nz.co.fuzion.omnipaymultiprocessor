<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

class HostedPageResults extends AbstractRequest
{
    const SECURITY_TOKEN = 'securityToken';

    public function getData()
    {
        $this->data = $this->getSecurityToken();

        return $this->data;
    }

    public function setSecurityToken($token)
    {
        return $this->setParameter(self::SECURITY_TOKEN, $token);
    }

    public function getSecurityToken()
    {
        return $this->getParameter(self::SECURITY_TOKEN);
    }

}