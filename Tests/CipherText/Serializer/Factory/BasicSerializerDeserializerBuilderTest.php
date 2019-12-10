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
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class BasicSerializerDeserializerBuilderTest extends AbstractExtendableTestCase
{
    /** @var  BasicSerializerDeserializerBuilder */
    protected $builder;

    public function setUp() : void
    {
        $this->builder = new BasicSerializerDeserializerBuilder();
    }

    public function testBuild()
    {
        $separator = $this->getFaker()->unique()->word;
        $this->assertEquals(
            new BasicSerializerDeserializer($separator),
            $this->builder->build(['separator' => $separator])
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->builder->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setDefaults')
                ->once()
                ->with(['separator' => ':'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('separator', 'string')
                ->andReturnSelf()
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('basic', $this->builder->getName());
    }
}
