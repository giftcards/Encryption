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
    /** @var DeserializerInterface[][] */
    protected $deserializers = array();
    /** @var DeserializerInterface[] */
    protected $sortedDeserializers = array();
    /** @var SerializerInterface[] */
    protected $sortedSerializers = array();
    
    public function addSerializer(SerializerInterface $serializer, $priority = 0)
    {
        $this->sortedSerializers = false;
        if (!isset($this->serializers[$priority])) {
            $this->serializers[$priority] = array();
        }

        $this->serializers[$priority][] = $serializer;
        return $this;
    }
    
    public function addDeserializer(DeserializerInterface $deserializer, $priority = 0)
    {
        $this->sortedDeserializers = false;
        if (!isset($this->deserializers[$priority])) {
            $this->deserializers[$priority] = array();
        }
        
        $this->deserializers[$priority][] = $deserializer;
        return $this;
    }

    /**
     * @param $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        $this->sortSerializers();
        foreach ($this->sortedSerializers as $serializer) {
            if ($serializer->canSerialize($cipherText)) {
                return $serializer->serialize($cipherText);
            }
        }

        throw new FailedToSerializeException($cipherText);
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText)
    {
        $this->sortSerializers();
        foreach ($this->sortedSerializers as $serializer) {
            if ($serializer->canSerialize($cipherText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $string
     * @return CipherTextInterface
     */
    public function deserialize($string)
    {
        $this->sortDeserializers();
        foreach ($this->sortedDeserializers as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return $serializer->deserialize($string);
            }
        }

        throw new FailedToDeserializeException($string);
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        $this->sortDeserializers();
        foreach ($this->sortedDeserializers as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return true;
            }
        }

        return false;
    }

    protected function sortSerializers()
    {
        if ($this->sortedSerializers || !$this->serializers) {
            return;
        }

        krsort($this->serializers);
        $this->sortedSerializers = call_user_func_array('array_merge', $this->serializers);
    }
    protected function sortDeserializers()
    {
        if ($this->sortedDeserializers || !$this->deserializers) {
            return;
        }
        
        krsort($this->deserializers);
        $this->sortedDeserializers = call_user_func_array('array_merge', $this->deserializers);
    }
}
