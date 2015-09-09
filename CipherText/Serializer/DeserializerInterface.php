<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 7:04 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer;

use Giftcards\Encryption\CipherText\CipherTextInterface;

interface DeserializerInterface
{
    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function deserialize($string);

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string);
}