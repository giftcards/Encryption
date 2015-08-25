<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 6:13 PM
 */

namespace Omni\Encryption\Tests\CipherText;

use Omni\Encryption\CipherText\Rotator\RotatorRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class RotatorRegistryTest extends AbstractExtendableTestCase
{
    /** @var  RotatorRegistry */
    protected $registry;

    public function setUp()
    {
        $this->registry = new RotatorRegistry();
    }
    
    public function testGettersSetters()
    {
        $rotator1 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator1Name = $this->getFaker()->unique()->word;
        $rotator2 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator2Name = $this->getFaker()->unique()->word;
        $rotator3 = \Mockery::mock('Omni\Encryption\CipherText\Rotator\RotatorInterface');
        $rotator3Name = $this->getFaker()->unique()->word;

        $this->registry
            ->set($rotator1Name, $rotator1)
            ->set($rotator2Name, $rotator2)
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

        $this->registry
            ->set($rotator1Name, $rotator1)
            ->set($rotator2Name, $rotator2)
            ->set($rotator3Name, $rotator3)
            ->get($this->getFaker()->unique()->word)
        ;
    }
}
