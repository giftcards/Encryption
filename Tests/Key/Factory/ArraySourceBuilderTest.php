<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\Key\Factory;

use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\Factory\ArraySourceBuilder;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ArraySourceBuilderTest extends AbstractExtendableTestCase
{
    /** @var  ArraySourceBuilder */
    protected $factory;

    public function setUp() : void
    {
        $this->factory = new ArraySourceBuilder();
    }

    public function testBuild()
    {
        $keys = [
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        ];
        $this->assertEquals(new ArraySource($keys), $this->factory->build(['keys' => $keys]));
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(['keys'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('keys', 'array')
                ->andReturnSelf()
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('array', $this->factory->getName());
    }
}
