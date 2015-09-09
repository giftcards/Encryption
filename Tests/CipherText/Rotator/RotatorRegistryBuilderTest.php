<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 4:33 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Mockery\MockInterface;
use Giftcards\Encryption\Cipher\CipherRegistry;
use Giftcards\Encryption\CipherText\Rotator\Factory\DatabaseTableRotatorBuilder;
use Giftcards\Encryption\CipherText\Rotator\Factory\DoctrineDBALRotatorBuilder;
use Giftcards\Encryption\CipherText\Rotator\RotatorRegistry;
use Giftcards\Encryption\CipherText\Rotator\RotatorRegistryBuilder;
use Giftcards\Encryption\Factory\Factory;
use Giftcards\Encryption\Tests\AbstractTestCase;

class RotatorRegistryBuilderTest extends AbstractTestCase
{
    /** @var  RotatorRegistryBuilder */
    protected $builder;
    /** @var  MockInterface */
    protected $rotator1;
    /** @var  MockInterface */
    protected $rotator2;
    /** @var  MockInterface */
    protected $factory;

    public function setUp()
    {
        $this->rotator1 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $this->rotator2 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $this->factory = \Mockery::mock('Giftcards\Encryption\Factory\Factory');
        $this->builder = new RotatorRegistryBuilder(
            $this->factory
        );
    }

    public function testNewInstance()
    {
        $this->assertEquals(new RotatorRegistryBuilder(new Factory(array(
            new DatabaseTableRotatorBuilder(),
            new DoctrineDBALRotatorBuilder(),
        ))), RotatorRegistryBuilder::newInstance());
    }
    
    public function testBuildWithBuilders()
    {
        $name1 = $this->getFaker()->unique()->word;
        $name2 = $this->getFaker()->unique()->word;
        $this->builder
            ->set($name1, $this->rotator1)
            ->set($name2, $this->rotator2)
        ;

        $registry = new RotatorRegistry();
        $registry
            ->set($name1, $this->rotator1)
            ->set($name2, $this->rotator2)
        ;
        $this->assertEquals($registry, $this->builder->build());
    }

    public function testBuildWithNamedBuilders()
    {
        $name1 = $this->getFaker()->unique()->word;
        $name2 = $this->getFaker()->unique()->word;
        $name3 = $this->getFaker()->unique()->word;
        $factoryName = $this->getFaker()->unique()->word;
        $factoryOptions = array(
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
        );
        $source3 = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\RotatorInterface');
        $this->factory
            ->shouldReceive('create')
            ->once()
            ->with($factoryName, $factoryOptions)
            ->andReturn($source3)
        ;
        $this->builder
            ->set($name1, $this->rotator1)
            ->set($name2, $this->rotator2)
            ->set($name3, $factoryName, $factoryOptions)
        ;


        $registry = new RotatorRegistry();
        $registry
            ->set($name1, $this->rotator1)
            ->set($name2, $this->rotator2)
            ->set($name3, $source3)
        ;
        $this->assertEquals($registry, $this->builder->build());
    }

    public function testGetFactory()
    {
        $this->assertSame($this->factory, $this->builder->getFactory());
    }
}
