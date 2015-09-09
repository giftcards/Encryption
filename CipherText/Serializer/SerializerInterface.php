<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/18/15
 * Time: 5:54 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer;

use Giftcards\Encryption\CipherText\CipherTextInterface;

interface SerializerInterface
{
    /**
     * @param CipherTextInterface $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText);

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText);
}
