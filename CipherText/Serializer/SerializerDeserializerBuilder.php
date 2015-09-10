<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:34 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer;

use Giftcards\Encryption\CipherText\Serializer\Factory\BasicSerializerDeserializerBuilder;
use Giftcards\Encryption\CipherText\Serializer\Factory\NoProfileSerializerDeserializerBuilder;
use Giftcards\Encryption\Factory\Factory;
use Giftcards\Encryption\Profile\ProfileRegistry;

class SerializerDeserializerBuilder
{
    protected $serializerFactory;
    protected $deserializerFactory;
    /** @var  SerializerInterface[] */
    protected $serializers = array();
    /** @var  DeserializerInterface[] */
    protected $deserializers = array();

    public static function newInstance(ProfileRegistry $profileRegistry = null)
    {
        return new static(
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\SerializerInterface',
                array(
                    new NoProfileSerializerDeserializerBuilder($profileRegistry),
                    new BasicSerializerDeserializerBuilder()
                )
            ),
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\DeserializerInterface',
                array(
                    new NoProfileSerializerDeserializerBuilder($profileRegistry),
                    new BasicSerializerDeserializerBuilder()
                )
            )
        );
    }

    public function __construct(Factory $serializerFactory, Factory $deserializerFactory)
    {
        $this->serializerFactory = $serializerFactory;
        $this->deserializerFactory = $deserializerFactory;
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

    public function addSerializer($serializer, array $options = array())
    {
        if (!$serializer instanceof SerializerInterface) {
            $serializer = $this->serializerFactory->create($serializer, $options);
        }
        
        $this->serializers[] = $serializer;
        return $this;
    }

    public function addDeserializer($deserializer, array $options = array())
    {
        if (!$deserializer instanceof DeserializerInterface) {
            $deserializer = $this->deserializerFactory->create($deserializer, $options);
        }
        
        $this->deserializers[] = $deserializer;
        return $this;
    }

    /**
     * @return Factory
     */
    public function getSerializerFactory()
    {
        return $this->serializerFactory;
    }

    /**
     * @return Factory
     */
    public function getDeserializerFactory()
    {
        return $this->deserializerFactory;
    }
}
