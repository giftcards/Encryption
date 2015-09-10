<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/15
 * Time: 1:28 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer\Factory;

use Giftcards\Encryption\CipherText\Serializer\BasicSerializerDeserializer;
use Giftcards\Encryption\CipherText\Serializer\Factory\BasicSerializerDeserializerBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class BasicSerializerDeserializerBuilderTest extends AbstractTestCase
{
    /** @var  BasicSerializerDeserializerBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new BasicSerializerDeserializerBuilder();
    }

    public function testBuild()
    {
        $separator = $this->getFaker()->unique()->word;
        $this->assertEquals(
            new BasicSerializerDeserializer($separator),
            $this->builder->build(array('separator' => $separator))
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->builder->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setDefaults')
                ->once()
                ->with(array('separator' => ':'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array('separator' => 'string'))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('basic', $this->builder->getName());
    }
}
