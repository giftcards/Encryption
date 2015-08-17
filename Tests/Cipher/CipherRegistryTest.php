<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 6:17 PM
 */

namespace Omni\Encryption\Tests\Cipher;

use Omni\Encryption\Cipher\CipherRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class CipherRegistryTest extends AbstractExtendableTestCase
{
    /** @var  CipherRegistry */
    protected $registry;

    public function setUp()
    {
        $this->registry = new CipherRegistry();
    }

    public function testGettersSetters()
    {
        $encryptor1Name = $this->getFaker()->unique()->word;
        $encryptor1 = \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor1Name)
            ->getMock()
        ;
        $encryptor2Name = $this->getFaker()->unique()->word;
        $encryptor2 = \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor2Name)
            ->getMock()
        ;
        $encryptor3Name = $this->getFaker()->unique()->word;
        $encryptor3 = \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor3Name)
            ->getMock()
        ;
        $this->assertSame($this->registry, $this->registry->add($encryptor1));
        $this->assertSame($this->registry, $this->registry->add($encryptor2));
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
    public function testGetWithMissingEncryptor()
    {
        $encryptor1Name = $this->getFaker()->unique()->word;
        $encryptor1 = \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn($encryptor1Name)
            ->getMock()
        ;
        $encryptor2Name = $this->getFaker()->unique()->word;
        $encryptor2 = \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
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
