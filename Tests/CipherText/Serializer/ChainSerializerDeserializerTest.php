<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:17 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer;

use Mockery;
use Mockery\MockInterface;
use Giftcards\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ChainSerializerDeserializerTest extends AbstractExtendableTestCase
{
    /** @var  ChainSerializerDeserializer */
    protected $chain;
    /** @var  MockInterface */
    protected $serializer1;
    /** @var  MockInterface */
    protected $serializer2;
    /** @var  MockInterface */
    protected $serializer3;
    /** @var  MockInterface */
    protected $deserializer1;
    /** @var  MockInterface */
    protected $deserializer2;
    /** @var  MockInterface */
    protected $deserializer3;

    public function setUp() : void
    {
        $this->chain = new ChainSerializerDeserializer();
        $this->chain
            ->addSerializer($this->serializer1 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface'))
            ->addSerializer($this->serializer2 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface'))
            ->addSerializer($this->serializer3 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\SerializerInterface'))
            ->addDeserializer($this->deserializer1 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface'))
            ->addDeserializer($this->deserializer2 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface'))
            ->addDeserializer($this->deserializer3 = Mockery::mock('Giftcards\Encryption\CipherText\Serializer\DeserializerInterface'))
        ;
    }

    public function testCanSerialize()
    {
        $cipherText = Mockery::mock('Giftcards\Encryption\CipherText\CipherTextInterface');
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
        $cipherText = Mockery::mock('Giftcards\Encryption\CipherText\CipherTextInterface');
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

    public function testSerializeWithNoSerializerAbleToSerialize()
    {
        $this->expectException('\Giftcards\Encryption\CipherText\Serializer\FailedToSerializeException');
        $cipherText = Mockery::mock('Giftcards\Encryption\CipherText\CipherTextInterface');
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
        $this->deserializer1
            ->shouldReceive('canDeserialize')
            ->twice()
            ->with($string)
            ->andReturn(false, false)
        ;
        $this->deserializer2
            ->shouldReceive('canDeserialize')
            ->twice()
            ->with($string)
            ->andReturn(true, false)
        ;
        $this->deserializer3
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
        $cipherText = Mockery::mock('Giftcards\Encryption\CipherText\CipherTextInterface');
        $this->deserializer1
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
        ;
        $this->deserializer2
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

    public function testDeserializeWithNoSerializerAbleToDeserialize()
    {
        $this->expectException('\Giftcards\Encryption\CipherText\Serializer\FailedToDeserializeException');
        $string = $this->getFaker()->word;
        $this->deserializer1
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
        ;
        $this->deserializer2
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
            ->getMock()
        ;
        $this->deserializer3
            ->shouldReceive('canDeserialize')
            ->once()
            ->with($string)
            ->andReturn(false)
            ->getMock()
        ;
        $this->chain->deserialize($string);
    }
}
