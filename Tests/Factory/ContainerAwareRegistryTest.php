<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/15
 * Time: 4:12 PM
 */

namespace Giftcards\Encryption\Tests\Factory;

use Giftcards\Encryption\Factory\ContainerAwareRegistry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerAwareRegistry $registry
 */
class ContainerAwareRegistryTest extends RegistryTest
{
    /** @var  ContainerInterface */
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->registry = new ContainerAwareRegistry($this->container);
    }

    public function testGettersSettersIncludingServiceIds()
    {
        $builder1Name = $this->getFaker()->unique()->word;
        $builder1 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder1Name)
            ->getMock()
        ;
        $builder2Name = $this->getFaker()->unique()->word;
        $builder2 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder2Name)
            ->getMock()
        ;
        $builder3Name = $this->getFaker()->unique()->word;
        $builder3 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder3Name)
            ->getMock()
        ;
        $builder2ServiceId = 'builder2';
        $this->container->set($builder2ServiceId, $builder2);
        $this->assertSame($this->registry, $this->registry->add($builder1));
        $this->assertSame($this->registry, $this->registry->setServiceId($builder2Name, $builder2ServiceId));
        $this->assertSame($this->registry, $this->registry->add($builder3));
        $this->assertTrue($this->registry->has($builder1Name));
        $this->assertSame($builder1, $this->registry->get($builder1Name));
        $this->assertTrue($this->registry->has($builder2Name));
        $this->assertSame($builder2, $this->registry->get($builder2Name));
        $this->assertTrue($this->registry->has($builder3Name));
        $this->assertSame($builder3, $this->registry->get($builder3Name));
        $this->assertFalse($this->registry->has($this->getFaker()->unique()->word));
        $this->assertEquals(array(
            $builder1Name => $builder1,
            $builder2Name => $builder2,
            $builder3Name => $builder3,
        ), $this->registry->all());
    }

    /**
     * @expectedException \Giftcards\Encryption\Factory\BuilderNotFoundException
     */
    public function testGetWithMissingEncryptorIncludingServiceIds()
    {
        $builder1Name = $this->getFaker()->unique()->word;
        $builder1 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder1Name)
            ->getMock()
        ;
        $builder2Name = $this->getFaker()->unique()->word;
        $builder2 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder2Name)
            ->getMock()
        ;
        $this->assertSame($this->registry, $this->registry->add($builder1));
        $this->assertSame($this->registry, $this->registry->add($builder2));
        $this->assertSame($builder1, $this->registry->get($builder1Name));
        $this->registry->get($this->getFaker()->unique()->word);
    }
}
