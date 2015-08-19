<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/18/15
 * Time: 5:54 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

interface SerializerInterface
{
    /**
     * @param CipherTextInterface $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText);

    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function unserialize($string);
}
