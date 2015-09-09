<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:58 PM
 */

namespace Giftcards\Encryption\Tests\CipherText;

use Giftcards\Encryption\CipherText\CipherText;
use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Tests\AbstractTestCase;

class CipherTextTest extends AbstractTestCase
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
