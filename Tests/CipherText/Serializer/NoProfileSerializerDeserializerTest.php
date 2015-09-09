<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 1:06 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\Serializer\NoProfileSerializerDeserializer;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class NoProfileSerializerDeserializerTest extends AbstractExtendableTestCase
{
    /** @var  NoProfileSerializerDeserializer */
    protected $serializer;
    protected $profile;

    public function setUp()
    {
        $this->profile = new Profile('', '');
        $this->serializer = new NoProfileSerializerDeserializer($this->profile);
    }

    public function testCanners()
    {
        $this->assertTrue($this->serializer->canDeserialize(''));
        $this->assertTrue($this->serializer->canSerialize(new CipherText('', new Profile('', ''))));
        $this->assertFalse($this->serializer->canSerialize(new CipherText('', new Profile('sdsd', ''))));
        $this->assertFalse($this->serializer->canSerialize(new CipherText('', new Profile('', 'ddd'))));
    }

    public function testSerialize()
    {
        $text = $this->getFaker()->word;
        $this->assertEquals($text, $this->serializer->serialize(new CipherText($text, new Profile('', ''))));
    }

    /**
     * @expectedException \Omni\Encryption\CipherText\Serializer\FailedToSerializeException
     */
    public function testSerializeWhereCant()
    {
        $text = $this->getFaker()->word;
        $this->assertEquals($text, $this->serializer->serialize(new CipherText($text, new Profile('dfdfdf', ''))));
    }

    public function testDeserialize()
    {
        $text = $this->getFaker()->word;
        $this->assertEquals(new CipherText($text, $this->profile), $this->serializer->deserialize($text));
    }
}
