<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 6/9/16
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\CipherText;

use Giftcards\Encryption\CipherText\ProfilelessChipherText;
use Giftcards\Encryption\Tests\AbstractTestCase;

class ProfilelessChipherTextTest extends AbstractTestCase
{
    public function testGetters()
    {
        $text = $this->getFaker()->unique()->sentence;
        $cipherText = new ProfilelessChipherText($text);
        $this->assertEquals($text, $cipherText->getText());
    }

    /**
     * @expectedException \Giftcards\Encryption\Profile\NoProfileException
     */
    public function testGetProfile()
    {
        $cipherText = new ProfilelessChipherText('');
        $cipherText->getProfile();
    }
}
