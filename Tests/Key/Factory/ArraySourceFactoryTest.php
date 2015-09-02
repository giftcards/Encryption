<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Omni\Encryption\Tests\Key\Factory;

use Omni\Encryption\Key\ArraySource;
use Omni\Encryption\Key\Factory\ArraySourceFactory;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ArraySourceFactoryTest extends AbstractExtendableTestCase
{
    /** @var  ArraySourceFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ArraySourceFactory();
    }

    public function testBuild()
    {
        $keys = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        );
        $this->assertEquals(new ArraySource($keys), $this->factory->build(array('keys' => $keys)));
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array('keys'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array('keys' => 'array'))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('array', $this->factory->getName());
    }
}
