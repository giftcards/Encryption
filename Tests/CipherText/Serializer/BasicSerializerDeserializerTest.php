<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 12:36 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\Serializer\BasicSerializerDeserializer;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class BasicSerializerDeserializerTest extends AbstractExtendableTestCase
{
    /** @var  BasicSerializerDeserializer */
    protected $serializer;
    protected $separator;

    public function setUp()
    {
        $this->serializer = new BasicSerializerDeserializer(
            $this->separator = ':'
        );
    }

    public function testCanSerialize()
    {
        $this->assertTrue($this->serializer->canSerialize(new CipherText('', new Profile('', ''))));
    }

    public function testSerialize()
    {
        $keyName = $this->getFaker()->word;
        $cipher = $this->getFaker()->word;
        $text = $this->getFaker()->word;
        $this->assertEquals(
            base64_encode(json_encode(array('key_name' => $keyName, 'cipher' => $cipher))).$this->separator.base64_encode($text),
            $this->serializer->serialize(new CipherText($text, new Profile($cipher, $keyName)))
        );
    }

    public function testCanDeserialize()
    {
        $string1 = str_replace($this->separator, '', $this->getFaker()->word);
        $string2 = str_replace($this->separator, '', $this->getFaker()->word);
        $this->assertFalse($this->serializer->canDeserialize($string1));
        $this->assertFalse($this->serializer->canDeserialize(sprintf(
            '%s%s%s',
            $string1,
            $this->separator,
            $string2
        )));
        $keyName = $this->getFaker()->word;
        $cipher = $this->getFaker()->word;
        $text = $this->getFaker()->word;
        $this->assertTrue($this->serializer->canDeserialize(
            base64_encode(json_encode(array('key_name' => $keyName, 'cipher' => $cipher))).$this->separator.base64_encode($text)
        ));
    }

    public function testDeserialize()
    {
        $keyName = $this->getFaker()->word;
        $cipher = $this->getFaker()->word;
        $text = $this->getFaker()->word;
        $this->assertEquals(
            new CipherText($text, new Profile($cipher, $keyName)),
            $this->serializer->deserialize(
                base64_encode(json_encode(array('key_name' => $keyName, 'cipher' => $cipher))).$this->separator.base64_encode($text)
            )
        );
        
    }

    /**
     * @expectedException \Omni\Encryption\CipherText\Serializer\FailedToDeserializeException
     *
     */
    public function testDeserializeWhereCantDeserialize()
    {
        $string = str_replace($this->separator, '', $this->getFaker()->word);
        $this->serializer->deserialize($string);
    }
}
