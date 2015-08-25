<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:27 PM
 */

namespace Omni\Encryption\Tests\CipherText;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\StringableCipherText;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class StringableCipherTextTest extends AbstractExtendableTestCase
{
    public function testGetters()
    {
        $text = $this->getFaker()->unique()->word;
        $cipher = $this->getFaker()->unique()->word;
        $keyName = $this->getFaker()->unique()->word;
        $profile = new Profile(
            $cipher,
            $keyName
        );
        $inner = new CipherText(
            $text,
            $profile
        );
        $string = $this->getFaker()->unique()->word;
        $serializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface')
            ->shouldReceive('serialize')
            ->once()
            ->with($inner)
            ->andReturn($string)
            ->getMock()
        ;
        $cipherText = new StringableCipherText(
            $inner,
            $serializer
        );
        $this->assertEquals($text, $cipherText->getText());
        $this->assertSame($profile, $cipherText->getProfile());
        $this->assertEquals($string, $cipherText);
    }
}
