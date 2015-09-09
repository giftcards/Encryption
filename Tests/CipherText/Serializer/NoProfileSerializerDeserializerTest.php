<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 1:06 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer;

use Giftcards\Encryption\CipherText\CipherText;
use Giftcards\Encryption\CipherText\Serializer\NoProfileSerializerDeserializer;
use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Tests\AbstractTestCase;

class NoProfileSerializerDeserializerTest extends AbstractTestCase
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
     * @expectedException \Giftcards\Encryption\CipherText\Serializer\FailedToSerializeException
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
