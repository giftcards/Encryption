<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:21 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\ContainerAwareRotatorRegistry;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareRotatorRegistryTest extends AbstractTestCase
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
        $rotator1 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator1Name = $this->getFaker()->unique()->word;
        $rotator2 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator2Name = $this->getFaker()->unique()->word;
        $rotator3 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
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
     * @expectedException \Giftcards\Encryption\CipherText\Rotator\RotatorNotFoundException
     */
    public function testGetWhereNotThere()
    {
        $rotator1 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator1Name = $this->getFaker()->unique()->word;
        $rotator2 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator2Name = $this->getFaker()->unique()->word;
        $rotator3 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
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
