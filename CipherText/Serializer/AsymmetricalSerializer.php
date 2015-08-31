<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 3:33 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

class AsymmetricalSerializer implements SerializerInterface
{
    protected $serializer;
    protected $deserializer;

    /**
     * AsymmetricalSerializer constructor.
     * @param SerializerInterface $serializer
     * @param SerializerInterface $deserializer
     */
    public function __construct(SerializerInterface $serializer, SerializerInterface $deserializer)
    {
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        return $this->serializer->serialize($cipherText);
    }

    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function deserialize($string)
    {
        return $this->deserializer->deserialize($string);
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText)
    {
        return $this->serializer->canSerialize($cipherText);
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        return $this->deserializer->canDeserialize($string);
    }
}
