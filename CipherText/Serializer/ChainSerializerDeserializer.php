<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 5:26 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

class ChainSerializerDeserializer implements SerializerDeserializerInterface
{
    /** @var SerializerInterface[][] */
    protected $serializers = array();
    /** @var SerializerInterface[] */
    protected $sorted = array();

    public function add(SerializerInterface $serializer, $priority = 0)
    {
        $this->sorted = false;
        if (!isset($this->serializers[$priority])) {
            $this->serializers[$priority] = array();
        }
        
        $this->serializers[$priority][] = $serializer;
        return $this;
    }
    
    /**
     * @param $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canSerialize($cipherText)) {
                return $serializer->serialize($cipherText);
            }
        }

        throw new FailedToSerializeException($cipherText);
    }

    /**
     * @param $string
     * @return CipherTextInterface
     */
    public function deserialize($string)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return $serializer->deserialize($string);
            }
        }

        throw new FailedToDeserializeException($string);
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canSerialize($cipherText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return true;
            }
        }

        return false;
    }

    protected function sort()
    {
        if ($this->sorted || !$this->serializers) {
            return;
        }
        
        krsort($this->serializers);
        $this->sorted = call_user_func_array('array_merge', $this->serializers);
    }
}
