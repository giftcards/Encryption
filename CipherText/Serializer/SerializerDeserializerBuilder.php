<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:34 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

class SerializerDeserializerBuilder
{
    /** @var  SerializerInterface[] */
    protected $serializers = array();
    /** @var  DeserializerInterface[] */
    protected $deserializers = array();

    public static function newInstance()
    {
        return new static();
    }

    public function build()
    {
        $serializerDeserializer = new ChainSerializerDeserializer();

        foreach ($this->serializers as $serializer) {
            $serializerDeserializer->addSerializer($serializer);
        }

        foreach ($this->deserializers as $deserializer) {
            $serializerDeserializer->addDeserializer($deserializer);
        }

        return $serializerDeserializer;
    }

    public function addSerializer(SerializerInterface $serializer)
    {
        $this->serializers[] = $serializer;
        return $this;
    }

    public function addDeserializer(DeserializerInterface $deserializer)
    {
        $this->deserializers[] = $deserializer;
        return $this;
    }
}
