<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:17 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer;

use Giftcards\Encryption\CipherText\CipherTextInterface;

class FailedToSerializeException extends \RuntimeException
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
