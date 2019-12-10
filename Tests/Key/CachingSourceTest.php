<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 2:15 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Mockery;
use Mockery\MockInterface;
use Giftcards\Encryption\Key\CachingSource;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class CachingSourceTest extends AbstractExtendableTestCase
{
    /** @var  CachingSource */
    protected $source;
    /** @var  MockInterface */
    protected $inner;
    /** @var  MockInterface */
    protected $cache;

    public function setUp() : void
    {
        $this->source = new CachingSource(
            $this->cache = Mockery::mock('Doctrine\Common\Cache\Cache'),
            $this->inner = Mockery::mock('Giftcards\Encryption\Key\SourceInterface')
        );
    }

    public function testHas()
    {
        $key = $this->getFaker()->word;
        $has = $this->getFaker()->boolean;
        $this->inner
            ->shouldReceive('has')
            ->once()
            ->with($key)
            ->andReturn($has)
        ;
        $this->cache
            ->shouldReceive('contains')
            ->twice()
            ->with($key)
            ->andReturn(false, true)
        ;
        $this->assertEquals($has, $this->source->has($key));
        $this->assertTrue($this->source->has($key));
    }

    public function testGet()
    {
        $key = $this->getFaker()->word;
        $value = $this->getFaker()->word;
        $this->inner
            ->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($value)
        ;
        $this->cache
            ->shouldReceive('contains')
            ->twice()
            ->with($key)
            ->andReturn(false, true)
            ->getMock()
            ->shouldReceive('save')
            ->once()
            ->with($key, $value)
            ->getMock()
            ->shouldReceive('fetch')
            ->once()
            ->with($key)
            ->andReturn($value)
            ->getMock()
        ;
        $this->assertEquals($value, $this->source->get($key));
        $this->assertEquals($value, $this->source->get($key));
    }
}
