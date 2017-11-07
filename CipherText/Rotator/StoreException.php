<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 2:28 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Exception;

class StoreException extends Exception
{

    /**
     * StoreException constructor.
     * @param string $message
     * @param Exception $exception
     */
    public function __construct($message, $exception)
    {
        parent::__construct($message, 0, $exception);
    }
}