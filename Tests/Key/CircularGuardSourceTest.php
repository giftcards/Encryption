<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/17/15
 * Time: 12:40 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Exception;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\CircularGuardSource;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class CircularGuardSourceTest extends AbstractExtendableTestCase
{
    public function testNonCircularHas()
    {
        $key = $this->getFaker()->unique()->word;
        $value = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(new ArraySource([$key => $value]));
        $this->assertTrue($source->has($key));
        $this->assertTrue($source->has($key));
    }

    public function testNonCircularGet()
    {
        $key = $this->getFaker()->unique()->word;
        $value = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(new ArraySource([$key => $value]));
        $this->assertEquals($value, $source->get($key));
        $this->assertEquals($value, $source->get($key));
    }
    
    public function testCircularGet()
    {
        $this->expectException('\Giftcards\Encryption\Key\KeyNotFoundException');
        $key = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource($innerSource = new MockCircularSource());
        $innerSource->setInner($source);
        $source->get($key);
    }
    
    public function testCircularHas()
    {
        $key = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource($innerSource = new MockCircularSource());
        $innerSource->setInner($source);
        $this->assertFalse($source->has($key));
    }

    public function testHasWithException()
    {
        $exception = new Exception();
        $key = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(
            Mockery::mock('Giftcards\Encryption\Key\SourceInterface')
                ->shouldReceive('has')
                ->once()
                ->with($key)
                ->andThrow($exception)
                ->getMock()
                ->shouldReceive('has')
                ->once()
                ->with($key)
                ->andReturn(true)
                ->getMock()
        );
        try {
            $source->has($key);
        } catch (Exception $e) {
            $this->assertSame($e, $exception);
        }
        
        $this->assertTrue($source->has($key));
    }
}
