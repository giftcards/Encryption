<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:24 PM
 */

namespace Omni\Encryption\Tests\Encryptor;

use Omni\Encryption\Cipher\ContainerAwareCipherRegistry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerAwareCipherRegistry $registry
 */
class ContainerAwareEncryptorRegistryTest extends EncryptorRegistryTest
{
    /** @var  ContainerInterface */
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->registry = new ContainerAwareCipherRegistry($this->container);
    }

    public function testGettersSettersIncludingServiceIds()
    {
        $encryptor1Name = $this->getFaker()->unique()->word;
        $encryptor1 = \Mockery::mock('Omni\Encryption\Encryptor\EncryptorInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor1Name)
            ->getMock()
        ;
        $encryptor2Name = $this->getFaker()->unique()->word;
        $encryptor2 = \Mockery::mock('Omni\Encryption\Encryptor\EncryptorInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor2Name)
            ->getMock()
        ;
        $encryptor3Name = $this->getFaker()->unique()->word;
        $encryptor3 = \Mockery::mock('Omni\Encryption\Encryptor\EncryptorInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor3Name)
            ->getMock()
        ;
        $encryptor2ServiceId = 'encryptor2';
        $this->container->set($encryptor2ServiceId, $encryptor2);
        $this->assertSame($this->registry, $this->registry->add($encryptor1));
        $this->assertSame($this->registry, $this->registry->setServiceId($encryptor2Name, $encryptor2ServiceId));
        $this->assertSame($this->registry, $this->registry->add($encryptor3));
        $this->assertTrue($this->registry->has($encryptor1Name));
        $this->assertSame($encryptor1, $this->registry->get($encryptor1Name));
        $this->assertTrue($this->registry->has($encryptor2Name));
        $this->assertSame($encryptor2, $this->registry->get($encryptor2Name));
        $this->assertTrue($this->registry->has($encryptor3Name));
        $this->assertSame($encryptor3, $this->registry->get($encryptor3Name));
        $this->assertFalse($this->registry->has($this->getFaker()->unique()->word));
        $this->assertEquals(array(
            $encryptor1Name => $encryptor1,
            $encryptor2Name => $encryptor2,
            $encryptor3Name => $encryptor3,
        ), $this->registry->all());
    }

    /**
     * @expectedException \Omni\Encryption\Cipher\CipherNotFoundException
     */
    public function testGetWithMissingEncryptorIncludingServiceIds()
    {
        $encryptor1Name = $this->getFaker()->unique()->word;
        $encryptor1 = \Mockery::mock('Omni\Encryption\Encryptor\EncryptorInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor1Name)
            ->getMock()
        ;
        $encryptor2Name = $this->getFaker()->unique()->word;
        $encryptor2 = \Mockery::mock('Omni\Encryption\Encryptor\EncryptorInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor2Name)
            ->getMock()
        ;
        $this->assertSame($this->registry, $this->registry->add($encryptor1));
        $this->assertSame($this->registry, $this->registry->add($encryptor2));
        $this->assertSame($encryptor1, $this->registry->get($encryptor1Name));
        $this->registry->get($this->getFaker()->unique()->word);
    }
}
