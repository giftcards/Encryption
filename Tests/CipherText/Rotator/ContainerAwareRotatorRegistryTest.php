<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:21 PM
 */

namespace Omni\Encryption\Tests\CipherText\Rotator;

use Omni\Encryption\CipherText\Rotator\ContainerAwareRotatorRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareRotatorRegistryTest extends AbstractExtendableTestCase
{
    /** @var  Container */
    protected $container;
    /** @var  ContainerAwareRotatorRegistry */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ContainerAwareRotatorRegistry(
            $this->container = new Container()
        );
    }

    public function testGettersSetters()
    {
        $rotator1 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator1Name = $this->getFaker()->unique()->word;
        $rotator2 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator2Name = $this->getFaker()->unique()->word;
        $rotator3 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator3Name = $this->getFaker()->unique()->word;

        $this->container->set('rotator2', $rotator2);
        
        $this->registry
            ->set($rotator1Name, $rotator1)
            ->setServiceId($rotator2Name, 'rotator2')
            ->set($rotator3Name, $rotator3)
        ;
        $this->assertTrue($this->registry->has($rotator1Name));
        $this->assertSame($rotator1, $this->registry->get($rotator1Name));
        $this->assertTrue($this->registry->has($rotator2Name));
        $this->assertSame($rotator2, $this->registry->get($rotator2Name));
        $this->assertTrue($this->registry->has($rotator3Name));
        $this->assertSame($rotator3, $this->registry->get($rotator3Name));
        $this->assertSame(array(
            $rotator1Name => $rotator1,
            $rotator2Name => $rotator2,
            $rotator3Name => $rotator3,
        ), $this->registry->all());
    }

    /**
     * @expectedException \Omni\Encryption\CipherText\Rotator\RotatorNotFoundException
     */
    public function testGetWhereNotThere()
    {
        $rotator1 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator1Name = $this->getFaker()->unique()->word;
        $rotator2 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator2Name = $this->getFaker()->unique()->word;
        $rotator3 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator3Name = $this->getFaker()->unique()->word;
        $this->container->set('rotator2', $rotator2);

        $this->registry
            ->set($rotator1Name, $rotator1)
            ->setServiceId($rotator2Name, 'rotator2')
            ->set($rotator3Name, $rotator3)
            ->get($this->getFaker()->unique()->word)
        ;
    }
}
