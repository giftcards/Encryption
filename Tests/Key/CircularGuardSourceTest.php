<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/17/15
 * Time: 12:40 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\CircularGuardSource;
use Giftcards\Encryption\Tests\AbstractTestCase;

class CircularGuardSourceTest extends AbstractTestCase
{
    public function testNonCircularHas()
    {
        $key = $this->getFaker()->unique()->word;
        $value = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(new ArraySource(array($key => $value)));
        $this->assertTrue($source->has($key));
        $this->assertTrue($source->has($key));
    }

    public function testNonCircularGet()
    {
        $key = $this->getFaker()->unique()->word;
        $value = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(new ArraySource(array($key => $value)));
        $this->assertEquals($value, $source->get($key));
        $this->assertEquals($value, $source->get($key));
    }
    
    /**
     * @expectedException \Giftcards\Encryption\Key\KeyNotFoundException
     */
    public function testCircularGet()
    {
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
        $exception = new \Exception();
        $key = $this->getFaker()->unique()->word;
        $source = new CircularGuardSource(
            \Mockery::mock('Giftcards\Encryption\Key\SourceInterface')
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
        } catch (\Exception $e) {
            $this->assertSame($e, $exception);
        }
        
        $this->assertTrue($source->has($key));
    }
}
