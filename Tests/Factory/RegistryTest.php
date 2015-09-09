<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 5:38 PM
 */

namespace Omni\Encryption\Tests\Factory;

use Omni\Encryption\Factory\Registry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class RegistryTest extends AbstractExtendableTestCase
{
    /** @var  Registry */
    protected $registry;

    public function setUp()
    {
        $this->registry = new Registry();
    }

    public function testGettersSetters()
    {
        $builder1Name = $this->getFaker()->unique()->word;
        $builder1 = \Mockery::mock('Omni\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder1Name)
            ->getMock()
        ;
        $builder2Name = $this->getFaker()->unique()->word;
        $builder2 = \Mockery::mock('Omni\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder2Name)
            ->getMock()
        ;
        $builder3Name = $this->getFaker()->unique()->word;
        $builder3 = \Mockery::mock('Omni\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder3Name)
            ->getMock()
        ;
        $this->assertSame($this->registry, $this->registry->add($builder1));
        $this->assertSame($this->registry, $this->registry->add($builder2));
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
     * @expectedException \Omni\Encryption\Factory\BuilderNotFoundException
     */
    public function testGetWithMissingBuilder()
    {
        $builder1Name = $this->getFaker()->unique()->word;
        $builder1 = \Mockery::mock('Omni\Encryption\Factory\BuilderInterface')
            ->shouldReceive('getName')
            ->andReturn($builder1Name)
            ->getMock()
        ;
        $builder2Name = $this->getFaker()->unique()->word;
        $builder2 = \Mockery::mock('Omni\Encryption\Factory\BuilderInterface')
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
