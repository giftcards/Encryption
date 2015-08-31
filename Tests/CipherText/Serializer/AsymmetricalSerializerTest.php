<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 4:57 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Mockery\MockInterface;
use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\Serializer\AsymmetricalSerializer;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class AsymmetricalSerializerTest extends AbstractExtendableTestCase
{
    /** @var  AsymmetricalSerializer */
    protected $serializer;
    /** @var  MockInterface */
    protected $innerSerializer;
    /** @var  MockInterface */
    protected $innerDeserializer;

    public function setUp()
    {
        $this->serializer = new AsymmetricalSerializer(
            $this->innerSerializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface'),
            $this->innerDeserializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface')
        );
    }

    public function testSerialize()
    {
        $cipherText = new CipherText('', new Profile('', ''));
        $serialized = $this->getFaker()->unique()->word;
        $this->innerSerializer
            ->shouldReceive('serialize')
            ->once()
            ->with($cipherText)
            ->andReturn($serialized)
        ;
        $this->assertEquals($serialized, $this->serializer->serialize($cipherText));
    }

    public function testCanSerialize()
    {
        $cipherText = new CipherText('', new Profile('', ''));
        $this->innerSerializer
            ->shouldReceive('canSerialize')
            ->twice()
            ->with($cipherText)
            ->andReturn(true, false)
        ;
        $this->assertTrue($this->serializer->canSerialize($cipherText));
        $this->assertFalse($this->serializer->canSerialize($cipherText));
    }

    public function testDeserialize()
    {
        $cipherText = new CipherText('', new Profile('', ''));
        $serialized = $this->getFaker()->unique()->word;
        $this->innerDeserializer
            ->shouldReceive('deserialize')
            ->once()
            ->with($serialized)
            ->andReturn($cipherText)
        ;
        $this->assertSame($cipherText, $this->serializer->deserialize($serialized));
    }

    public function testCanDeserialize()
    {
        $serialized = $this->getFaker()->unique()->word;
        $this->innerDeserializer
            ->shouldReceive('canDeserialize')
            ->twice()
            ->with($serialized)
            ->andReturn(true, false)
        ;
        $this->assertTrue($this->serializer->canDeserialize($serialized));
        $this->assertFalse($this->serializer->canDeserialize($serialized));
    }
}
