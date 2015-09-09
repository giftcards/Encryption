<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:40 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Omni\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Omni\Encryption\CipherText\Serializer\SerializerDeserializerBuilder;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class SerializerDeserializerBuilderTest extends AbstractExtendableTestCase
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
        $serializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface');
        $deserializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\DeserializerInterface');
        $this->builder
            ->addSerializer($serializer)
            ->addDeserializer($deserializer)
        ;
        $serializerDeserializer = new ChainSerializerDeserializer();
        $serializerDeserializer->addSerializer($serializer)->addDeserializer($deserializer);
        $this->assertEquals($serializerDeserializer, $this->builder->build());
    }
}
