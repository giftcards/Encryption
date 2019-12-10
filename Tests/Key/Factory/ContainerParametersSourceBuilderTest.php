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

use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerParametersSourceBuilderTest extends AbstractExtendableTestCase
{
    /** @var  ContainerParametersSourceBuilder */
    protected $factory;

    public function setUp() : void
    {
        $this->factory = new ContainerParametersSourceBuilder();
    }

    public function testBuild()
    {
        $container = new Container();
        $this->assertEquals(
            new ContainerParametersSource($container),
            $this->factory->build(['container' => $container])
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(['container'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('container', 'Symfony\Component\DependencyInjection\ContainerInterface')
                ->andReturnSelf()
                ->getMock()
        );
        $container = new Container();
        $factory = new ContainerParametersSourceBuilder($container);
        $factory->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(['container'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('container', 'Symfony\Component\DependencyInjection\ContainerInterface')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with(['container' => $container])
                ->andReturnSelf()
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('container_parameters', $this->factory->getName());
    }
}
