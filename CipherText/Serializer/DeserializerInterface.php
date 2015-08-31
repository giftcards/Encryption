<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 7:04 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

interface DeserializerInterface
{
    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function deserialize($string);

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText);

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string);
}