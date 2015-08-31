<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:17 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Mockery\MockInterface;
use Omni\Encryption\CipherText\Serializer\ChainDeserializer;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ChainDeserializerTest extends AbstractExtendableTestCase
{
    /** @var  ChainDeserializer */
    protected $chain;
    /** @var  MockInterface */
    protected $deserializer1;
    /** @var  MockInterface */
    protected $deserializer2;
    /** @var  MockInterface */
    protected $deserializer3;

    public function setUp()
    {
        $this->chain = new ChainDeserializer();
        $this->chain
            ->add($this->deserializer1 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\DeserializerInterface'))
            ->add($this->deserializer2 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\DeserializerInterface'))
            ->add($this->deserializer3 = \Mockery::mock('Omni\Encryption\CipherText\Serializer\DeserializerInterface'))
        ;
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
        $cipherText = \Mockery::mock('Omni\Encryption\CipherText\CipherTextInterface');
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

    /**
     * @expectedException \Omni\Encryption\CipherText\Serializer\FailedToDeserializeException
     */
    public function testDeserializeWithNoSerializerAbleToDeserialize()
    {
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
