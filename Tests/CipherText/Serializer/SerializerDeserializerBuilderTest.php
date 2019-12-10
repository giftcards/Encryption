<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:40 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer;

use Giftcards\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Giftcards\Encryption\CipherText\Serializer\Factory\BasicSerializerDeserializerBuilder;
use Giftcards\Encryption\CipherText\Serializer\Factory\NoProfileSerializerDeserializerBuilder;
use Giftcards\Encryption\CipherText\Serializer\SerializerDeserializerBuilder;
use Giftcards\Encryption\Factory\Factory;

use Mockery;
use Mockery\MockInterface;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class SerializerDeserializerBuilderTest extends AbstractExtendableTestCase
{
    /** @var  SerializerDeserializerBuilder */
    protected $builder;
    /** @var  MockInterface */
    protected $serializerFactory;
    /** @var  MockInterface */
    protected $deserializerFactory;

    public function setUp() : void
    {
        $this->builder = new SerializerDeserializerBuilder(
            $this->serializerFactory = Mockery::mock('Giftcards\Encryption\Factory\Factory'),
            $this->deserializerFactory = Mockery::mock('Giftcards\Encryption\Factory\Factory')
        );
    }

    public function testNewInstance()
    {
        $this->assertEquals(new SerializerDeserializerBuilder(
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\SerializerInterface',
                [
                    new NoProfileSerializerDeserializerBuilder(),
                    new BasicSerializerDeserializerBuilder()
                ]
            ),
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\DeserializerInterface',
                [
                    new NoProfileSerializerDeserializerBuilder(),
                    new BasicSerializerDeserializerBuilder()
                ]
            )
        ), SerializerDeserializerBuilder::newInstance());
        $profileRegistry = Mockery::mock('Giftcards\Encryption\Profile\ProfileRegistry');
        $this->assertEquals(new SerializerDeserializerBuilder(
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\SerializerInterface',
                [
                    new NoProfileSerializerDeserializerBuilder($profileRegistry),
                    new BasicSerializerDeserializerBuilder()
                ]
            ),
            new Factory(
                'Giftcards\Encryption\CipherText\Serializer\DeserializerInterface',
                [
                    new NoProfileSerializerDeserializerBuilder($profileRegistry),
                    new BasicSerializerDeserializerBuilder()
                ]
            )
        ), SerializerDeserializerBuilder::newInstance($profileRegistry));
    }

    public function testBuild()
    {
        $serializer1 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface');
        $deserializer1 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface');
        $serializer2 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface');
        $deserializer2 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface');
        $serializerFactoryName = $this->getFaker()->unique()->word;
        $serializerFactoryOptions = [
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
        ];
        $this->serializerFactory
            ->shouldReceive('create')
            ->once()
            ->with($serializerFactoryName, $serializerFactoryOptions)
            ->andReturn($serializer2)
        ;
        $deserializerFactoryName = $this->getFaker()->unique()->word;
        $deserializerFactoryOptions = [
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
        ];
        $this->deserializerFactory
            ->shouldReceive('create')
            ->once()
            ->with($deserializerFactoryName, $deserializerFactoryOptions)
            ->andReturn($deserializer2)
        ;
        $this->builder
            ->addSerializer($serializer1)
            ->addDeserializer($deserializer1)
            ->addSerializer($serializerFactoryName, $serializerFactoryOptions)
            ->addDeserializer($deserializerFactoryName, $deserializerFactoryOptions)
        ;
        $serializerDeserializer = new ChainSerializerDeserializer();
        $serializerDeserializer
            ->addSerializer($serializer1)
            ->addDeserializer($deserializer1)
            ->addSerializer($serializer2)
            ->addDeserializer($deserializer2)
        ;
        $this->assertEquals($serializerDeserializer, $this->builder->build());
    }

    public function testFactoryGetters()
    {
        $this->assertSame($this->serializerFactory, $this->builder->getSerializerFactory());
        $this->assertSame($this->deserializerFactory, $this->builder->getDeserializerFactory());
    }
}
