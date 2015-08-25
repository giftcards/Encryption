<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:17 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Mockery\MockInterface;
use Omni\Encryption\CipherText\Serializer\ChainSerializer;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ChainSerializerTest extends AbstractExtendableTestCase
{
    /** @var  ChainSerializer */
    protected $chain;
    /** @var  MockInterface */
    protected $serializer1;
    /** @var  MockInterface */
    protected $serializer2;
    /** @var  MockInterface */
    protected $serializer3;

    public function setUp()
    {
        $this->chain = new ChainSerializer();
        $this->chain
            ->add($this->serializer1 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface'))
            ->add($this->serializer2 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface'))
            ->add($this->serializer3 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface'))
        ;
    }

    public function testCanSerialize()
    {
        $cipherText = \Mockery::mock('Omni\Encryption\CipherText\CipherTextInterface');
        $this->serializer1
            ->shouldReceive('canSerialize')
            ->twice()
            ->with($cipherText)
            ->andReturn(false, false)
        ;
        $this->serializer2
            ->shouldReceive('canSerialize')
            ->twice()
            ->with($cipherText)
            ->andReturn(true, false)
        ;
        $this->serializer3
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(false)
        ;
        $this->assertTrue($this->chain->canSerialize($cipherText));
        $this->assertFalse($this->chain->canSerialize($cipherText));
    }

    public function testSerialize()
    {
        $cipherText = \Mockery::mock('Omni\Encryption\CipherText\CipherTextInterface');
        $string = $this->getFaker()->word;
        $this->serializer1
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(false)
        ;
        $this->serializer2
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('serialize')
            ->once()
            ->with($cipherText)
            ->andReturn($string)
            ->getMock()
        ;
        $this->assertEquals($string, $this->chain->serialize($cipherText));
    }

    /**
     * @expectedException \Omni\Encryption\CipherText\Serializer\FailedToSerializeException
     */
    public function testSerializeWithNoSerializerAbleToSerialize()
    {
        $cipherText = \Mockery::mock('Omni\Encryption\CipherText\CipherTextInterface');
        $this->serializer1
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(false)
        ;
        $this->serializer2
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(false)
            ->getMock()
        ;
        $this->serializer3
            ->shouldReceive('canSerialize')
            ->once()
            ->with($cipherText)
            ->andReturn(false)
            ->getMock()
        ;
        $this->chain->serialize($cipherText);
    }

    public function testCanDeserialize()
    {
        $string = $this->getFaker()->word;
        $this->serializer1
            ->shouldReceive('canDeserialize')
            ->twice()
            ->with($string)
            ->andReturn(false, false)
        ;
        $this->serializer2
            ->shouldReceive('canDeserialize')
            ->twice()
            ->with($string)
            ->andReturn(true, false)
        ;
        $this->serializer3
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
        ;
        $this->assertTrue($this->chain->canDeserialize($string));
        $this->assertFalse($this->chain->canDeserialize($string));
    }

    public function testDeserialize()
    {
        $string = $this->getFaker()->word;
        $cipherText = \Mockery::mock('Omni\Encryption\CipherText\CipherTextInterface');
        $this->serializer1
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
        ;
        $this->serializer2
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('deserialize')
            ->once()
            ->with($string)
            ->andReturn($cipherText)
            ->getMock()
        ;
        $this->assertSame($cipherText, $this->chain->deserialize($string));
    }

    /**
     * @expectedException \Omni\Encryption\CipherText\Serializer\FailedToDeserializeException
     */
    public function testDeserializeWithNoSerializerAbleToDeserialize()
    {
        $string = $this->getFaker()->word;
        $this->serializer1
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
        ;
        $this->serializer2
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
            ->getMock()
        ;
        $this->serializer3
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
            ->getMock()
        ;
        $this->chain->deserialize($string);
    }
}
