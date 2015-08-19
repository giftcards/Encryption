<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 5:18 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    /**
     * @param CipherTextInterface $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        if (!$this->canSerialize($cipherText)) {
            throw new FailedToSerializeException($cipherText, 'serialization is not supported for this cipher text');
        }
        
        return $this->doSerialize($cipherText);
    }

    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function deserialize($string)
    {
        if (!$this->canDeserialize($string)) {
            throw new FailedToDeserializeException($string, 'unserialization is not supported for this cipher text');
        }
        
        return $this->doDeserialize($string);
    }

    /**
     * @param $cipherText
     * @return string
     */
    abstract protected function doSerialize(CipherTextInterface $cipherText);

    /**
     * @param $string
     * @return CipherTextInterface
     */
    abstract protected function doDeserialize($string);
}
