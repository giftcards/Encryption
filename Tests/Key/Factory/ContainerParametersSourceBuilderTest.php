<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\Key\Factory;

use Giftcards\Encryption\Key\ContainerParametersSource;
use Giftcards\Encryption\Key\Factory\ContainerParametersSourceBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerParametersSourceBuilderTest extends AbstractTestCase
{
    /** @var  ContainerParametersSourceBuilder */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ContainerParametersSourceBuilder();
    }

    public function testBuild()
    {
        $container = new Container();
        $this->assertEquals(
            new ContainerParametersSource($container),
            $this->factory->build(array('container' => $container))
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array('container'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array('container' => 'Symfony\Component\DependencyInjection\ContainerInterface'))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
        $container = new Container();
        $factory = new ContainerParametersSourceBuilder($container);
        $factory->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array('container'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array('container' => 'Symfony\Component\DependencyInjection\ContainerInterface'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with(array('container' => $container))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('container_parameters', $this->factory->getName());
    }
}
