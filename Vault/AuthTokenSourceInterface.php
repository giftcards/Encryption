<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 5:59 PM
 */

namespace Giftcards\Encryption\Vault;

use Guzzle\Http\Message\Request;

interface AuthTokenSourceInterface
{
    /**
     * @param Request $request
     * @return string the token
     */
    public function getToken(Request $request);
}
