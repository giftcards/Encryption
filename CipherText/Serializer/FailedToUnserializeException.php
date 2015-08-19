<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:18 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

class FailedToUnserializeException extends \RuntimeException
{
    protected $string;

    /**
     * FailedToUnserializeException constructor.
     * @param string $string
     * @param string $message
     */
    public function __construct($string, $message = '')
    {
        $this->string = $string;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }
}
