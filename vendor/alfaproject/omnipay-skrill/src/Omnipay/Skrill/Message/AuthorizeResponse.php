<?php
namespace Omnipay\Skrill\Message;

/**
 * Skrill Authorize Response
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
class AuthorizeResponse extends Response
{
    /**
     * Get the session identifier to be submitted at the next step.
     *
     * @return string session id
     */
    public function getSessionId()
    {
        return isset($this->data->sid)
            ? (string) $this->data->sid
            : null;
    }
}
