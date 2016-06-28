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
use Giftcards\Encryption\Tests\AbstractTestCase;

class ArraySourceBuilderTest extends AbstractTestCase
{
    /** @var  ArraySourceBuilder */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ArraySourceBuilder();
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
                ->with('keys', 'array')
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('array', $this->factory->getName());
    }
}
