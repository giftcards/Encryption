<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:17 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

class FailedToSerializerException extends \RuntimeException
{
    protected $cipherText;

    /**
     * FailedToSerializerException constructor.
     * @param CipherTextInterface $cipherText
     * @param string $message
     */
    public function __construct(CipherTextInterface $cipherText, $message = '')
    {
        $this->cipherText = $cipherText;
        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getCipherText()
    {
        return $this->cipherText;
    }
}
