<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:40 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer;

use Giftcards\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Giftcards\Encryption\CipherText\Serializer\SerializerDeserializerBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class SerializerDeserializerBuilderTest extends AbstractTestCase
{
    /** @var  SerializerDeserializerBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new SerializerDeserializerBuilder();
    }

    public function testNewInstance()
    {
        $this->assertEquals(new SerializerDeserializerBuilder(), SerializerDeserializerBuilder::newInstance());
    }

    public function testBuild()
    {
        $serializer = \Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface');
        $deserializer = \Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface');
        $this->builder
            ->addSerializer($serializer)
            ->addDeserializer($deserializer)
        ;
        $serializerDeserializer = new ChainSerializerDeserializer();
        $serializerDeserializer->addSerializer($serializer)->addDeserializer($deserializer);
        $this->assertEquals($serializerDeserializer, $this->builder->build());
    }
}
