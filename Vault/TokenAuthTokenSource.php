<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 6:01 PM
 */

namespace Omni\Encryption\Vault;

use Guzzle\Http\Message\Request;

class TokenAuthTokenSource implements AuthTokenSourceInterface
{
    protected $token;

    /**
     * TokenAuthHandler constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getToken(Request $request)
    {
        return $this->token;
    }
}
