<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 6/9/16
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\CipherText;

use Giftcards\Encryption\CipherText\ProfilelessChipherText;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ProfilelessChipherTextTest extends AbstractExtendableTestCase
{
    public function testGetters()
    {
        $text = $this->getFaker()->unique()->sentence;
        $cipherText = new ProfilelessChipherText($text);
        $this->assertEquals($text, $cipherText->getText());
    }

    public function testGetProfile()
    {
        $this->expectException('\Giftcards\Encryption\Profile\NoProfileException');
        $cipherText = new ProfilelessChipherText('');
        $cipherText->getProfile();
    }
}
