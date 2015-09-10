<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 3:54 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Doctrine\Common\Cache\ArrayCache;
use Giftcards\Encryption\Key\Factory\ContainerParametersSourceBuilder;
use Mockery\MockInterface;
use Giftcards\Encryption\Factory\Factory;
use Giftcards\Encryption\Key\CachingSource;
use Giftcards\Encryption\Key\ChainSource;
use Giftcards\Encryption\Key\CombiningSource;
use Giftcards\Encryption\Key\Factory\ArraySourceBuilder;
use Giftcards\Encryption\Key\Factory\IniFileSourceBuilder;
use Giftcards\Encryption\Key\Factory\MongoSourceBuilder;
use Giftcards\Encryption\Key\Factory\VaultSourceBuilder;
use Giftcards\Encryption\Key\FallbackSource;
use Giftcards\Encryption\Key\MappingSource;
use Giftcards\Encryption\Key\NoneSource;
use Giftcards\Encryption\Key\PrefixKeyNameSource;
use Giftcards\Encryption\Key\SourceBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class SourceBuilderTest extends AbstractTestCase
{
    /** @var  SourceBuilder */
    protected $builder;
    /** @var  MockInterface */
    protected $source1;
    /** @var  MockInterface */
    protected $source2;
    /** @var  MockInterface */
    protected $factory;

    public function setUp()
    {
        $this->source1 = \Mockery::mock('Giftcards\Encryption\Key\SourceInterface');
        $this->source2 = \Mockery::mock('Giftcards\Encryption\Key\SourceInterface');
        $this->factory = \Mockery::mock('Giftcards\Encryption\Factory\Factory');
        $this->builder = new SourceBuilder(
            $this->factory
        );
    }

    public function testNewInstance()
    {
        $this->assertEquals(new SourceBuilder(new Factory(
            'Giftcards\Encryption\Key\SourceInterface',
            array(
                new VaultSourceBuilder(),
                new MongoSourceBuilder(),
                new IniFileSourceBuilder(),
                new ArraySourceBuilder(),
                new ContainerParametersSourceBuilder()
            )
        )), SourceBuilder::newInstance());
    }

    public function testBuildWithFallbacks()
    {
        $fallbackses = array(
            $this->getFaker()->unique()->word => array(
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
            ),
            $this->getFaker()->unique()->word => array(
                $this->getFaker()->unique()->word,
            ),
        );
        foreach ($fallbackses as $name => $fallbacks) {
            foreach ($fallbacks as $fallback) {
                $this->builder->addFallback($name, $fallback);
            }
        }
        
        $this->builder
            ->add($this->source1)
            ->add($this->source2)
        ;

        $source = new ChainSource();
        $source
            ->add(new FallbackSource($fallbackses, $source))
            ->add($this->source1)
            ->add($this->source2)
        ;
        $this->assertEquals($source, $this->builder->build());
    }

    public function testBuildWithMap()
    {
        $map = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        );
        foreach ($map as $name => $mapped) {
            $this->builder->map($name, $mapped);
        }

        $this->builder
            ->add($this->source1)
            ->add($this->source2)
        ;

        $source = new ChainSource();
        $source
            ->add(new MappingSource($map, $source))
            ->add($this->source1)
            ->add($this->source2)
        ;
        $this->assertEquals($source, $this->builder->build());
    }

    public function testBuildWithCombined()
    {
        $combined = array(
            $this->getFaker()->unique()->word => array(
                CombiningSource::LEFT => $this->getFaker()->unique()->word,
                CombiningSource::RIGHT => $this->getFaker()->unique()->word,
            ),
            $this->getFaker()->unique()->word => array(
                CombiningSource::LEFT => $this->getFaker()->unique()->word,
                CombiningSource::RIGHT => $this->getFaker()->unique()->word,
            ),
            $this->getFaker()->unique()->word => array(
                CombiningSource::LEFT => $this->getFaker()->unique()->word,
                CombiningSource::RIGHT => $this->getFaker()->unique()->word,
            ),
        );
        foreach ($combined as $name => $leftRight) {
            $this->builder->combine(
                $leftRight[CombiningSource::LEFT],
                $leftRight[CombiningSource::RIGHT],
                $name
            );
        }

        $this->builder
            ->add($this->source1)
            ->add($this->source2)
        ;

        $source = new ChainSource();
        $source
            ->add(new CombiningSource($combined, $source))
            ->add($this->source1)
            ->add($this->source2)
        ;
        $this->assertEquals($source, $this->builder->build());
    }

    public function testBuildWithCaching()
    {
        $this->builder
            ->add($this->source1)
            ->add($this->source2)
        ;

        $source = new ChainSource();
        $source
            ->add($this->source1)
            ->add($this->source2)
        ;
        $source = new CachingSource(new ArrayCache(), $source);
        $this->assertEquals($source, $this->builder->cache()->build());
    }

    public function testBuildWithNoneIncluded()
    {
        $this->builder
            ->add($this->source1)
            ->add($this->source2)
        ;

        $source = new ChainSource();
        $source
            ->add($this->source1)
            ->add($this->source2)
            ->add(new NoneSource())
        ;
        $this->assertEquals($source, $this->builder->includeNone()->build());
    }

    public function testBuildWithNamedBuilders()
    {
        $factoryName = $this->getFaker()->unique()->word;
        $factoryOptions = array(
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
        );
        $source3 = \Mockery::mock('Giftcards\Encryption\Key\SourceInterface');
        $this->factory
            ->shouldReceive('create')
            ->once()
            ->with($factoryName, $factoryOptions)
            ->andReturn($source3)
        ;
        $this->builder
            ->add($this->source1)
            ->add($this->source2)
            ->add($factoryName, $factoryOptions, false)
        ;


        $source = new ChainSource();
        $source
            ->add($this->source1)
            ->add($this->source2)
            ->add($source3)
        ;
        $this->assertEquals($source, $this->builder->build());
    }

    public function testBuildWithNamedBuildersWithPrefixes()
    {
        $factoryName = $this->getFaker()->unique()->word;
        $factoryOptions = array(
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word =>$this->getFaker()->unique()->word,
        );
        $prefix = $this->getFaker()->unique()->word;
        $source3 = \Mockery::mock('Giftcards\Encryption\Key\SourceInterface');
        $this->factory
            ->shouldReceive('create')
            ->once()
            ->with($factoryName, $factoryOptions)
            ->andReturn($source3)
        ;
        $this->builder
            ->add($this->source1)
            ->add($this->source2, array(), $prefix)
            ->add($factoryName, $factoryOptions)
        ;


        $source = new ChainSource();
        $source
            ->add($this->source1)
            ->add(new PrefixKeyNameSource($prefix, $this->source2))
            ->add(new PrefixKeyNameSource($factoryName, $source3))
        ;
        $this->assertEquals($source, $this->builder->build());
    }

    public function testGetFactory()
    {
        $this->assertSame($this->factory, $this->builder->getFactory());
    }
}
