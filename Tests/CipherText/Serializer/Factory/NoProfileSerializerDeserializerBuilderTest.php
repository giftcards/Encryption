<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/15
 * Time: 1:28 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer\Factory;

use Giftcards\Encryption\CipherText\Serializer\Factory\NoProfileSerializerDeserializerBuilder;
use Giftcards\Encryption\CipherText\Serializer\NoProfileSerializerDeserializer;
use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Profile\ProfileRegistry;

use Giftcards\Encryption\Tests\Mock\Mockery\Matcher\EqualsMatcher;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoProfileSerializerDeserializerBuilderTest extends AbstractExtendableTestCase
{
    /** @var  NoProfileSerializerDeserializerBuilder */
    protected $builder;
    /** @var  NoProfileSerializerDeserializerBuilder */
    protected $builderWithProfileRegistry;
    /** @var  ProfileRegistry */
    protected $profileRegistry;

    public function setUp() : void
    {
        $this->builder = new NoProfileSerializerDeserializerBuilder();
        $this->builderWithProfileRegistry = new NoProfileSerializerDeserializerBuilder(
            $this->profileRegistry = new ProfileRegistry()
        );
    }

    public function testBuild()
    {
        $profile = $this->getFaker()->boolean ? new Profile(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word
        ) : null;
        $this->assertEquals(
            new NoProfileSerializerDeserializer($profile),
            $this->builder->build(['profile' => $profile])
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->builder->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setDefaults')
                ->once()
                ->with(['profile' => null])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('profile', ['Giftcards\Encryption\Profile\Profile', 'null'])
                ->andReturnSelf()
                ->getMock()
        );
        $this->builderWithProfileRegistry->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setDefaults')
                ->once()
                ->with(['profile' => null])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('profile', ['Giftcards\Encryption\Profile\Profile', 'null', 'string'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setNormalizer')
                ->once()
                ->with('profile', new EqualsMatcher(function () {
                }))
                ->andReturnSelf()
                ->getMock()
        );
        $resolver = new OptionsResolver();
        $this->builderWithProfileRegistry->configureOptionsResolver($resolver);
        $profile = new Profile(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word
        );
        $profileName = $this->getFaker()->unique()->word;
        $this->profileRegistry->set($profileName, $profile);
        $options = $resolver->resolve(['profile' => $profileName]);
        $this->assertSame($options, $resolver->resolve(['profile' => $profile]));
        $this->assertSame($profile, $options['profile']);
        $options = $resolver->resolve();
        $this->assertNull($options['profile']);
    }

    public function testGetName()
    {
        $this->assertEquals('no_profile', $this->builder->getName());
    }
}
