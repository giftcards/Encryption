<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:58 PM
 */

namespace Omni\Encryption\Tests\CipherText;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class CipherTextTest extends AbstractExtendableTestCase
{
    public function testGetters()
    {
        $profile = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $text = $this->getFaker()->word;
        $cipherText = new CipherText(
            $text,
            $profile
        );
        $this->assertSame($text, $cipherText->getText());
        $this->assertEquals($profile, $cipherText->getProfile());
    }
}
