<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 5:42 PM
 */

namespace Giftcards\Encryption\Tests\Factory;

use Mockery\MockInterface;
use Giftcards\Encryption\Factory\Factory;
use Giftcards\Encryption\Factory\Registry;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryTest extends AbstractTestCase
{
    /** @var  Factory */
    protected $factory;
    /** @var  MockInterface */
    protected $builder1;
    /** @var  MockInterface */
    protected $builder2;
    /** @var  MockInterface */
    protected $builder3;

    public function setUp()
    {
        $this->factory = new Factory(
            get_class(\Mockery::mock()),
            array(
                $this->builder1 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
                    ->shouldReceive('getName')
                    ->andReturn('builder1')
                    ->getMock()
                ,
                $this->builder2 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
                    ->shouldReceive('getName')
                    ->andReturn('builder2')
                    ->getMock()
                ,
                $this->builder3 = \Mockery::mock('Giftcards\Encryption\Factory\BuilderInterface')
                    ->shouldReceive('getName')
                    ->andReturn('builder3')
                    ->getMock()
                ,
            )
        );
    }

    public function testCreate()
    {
        $options = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        );
        $defaults = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique(
            )->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique(
            )->word,
        );
        $resolvedOptions = array_merge($options, $defaults);
        $object = \Mockery::mock();
        $this->builder2
            ->shouldReceive('configureOptionsResolver')
            ->once()
            ->with('Symfony\Component\OptionsResolver\OptionsResolver')
            ->andReturnUsing(function (OptionsResolver $resolver) use ($options, $defaults) {
                $resolver
                    ->setRequired(array_keys($options))
                    ->setDefaults($defaults)
                ;

            })
            ->getMock()
            ->shouldReceive('build')
            ->once()
            ->with($resolvedOptions)
            ->andReturn($object)
            ->getMock()
        ;
        $this->assertSame($object, $this->factory->create('builder2', $options));
    }

    /**
     * @expectedException \Giftcards\Encryption\Factory\WrongTypeBuiltException
     */
    public function testCreateWhereBadInstaceReturned()
    {
        $options = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        );
        $defaults = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique(
            )->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique(
            )->word,
        );
        $resolvedOptions = array_merge($options, $defaults);
        $object = new \stdClass();
        $this->builder2
            ->shouldReceive('configureOptionsResolver')
            ->once()
            ->with('Symfony\Component\OptionsResolver\OptionsResolver')
            ->andReturnUsing(function (OptionsResolver $resolver) use ($options, $defaults) {
                $resolver
                    ->setRequired(array_keys($options))
                    ->setDefaults($defaults)
                ;

            })
            ->getMock()
            ->shouldReceive('build')
            ->once()
            ->with($resolvedOptions)
            ->andReturn($object)
            ->getMock()
        ;
        $this->factory->create('builder2', $options);
    }

    public function testGetRegistry()
    {
        $registry = new Registry();
        $factory = new Factory('', $registry);
        $this->assertSame($registry, $factory->getRegistry());
    }
}
