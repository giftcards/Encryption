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
}
