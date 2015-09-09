<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:45 PM
 */

namespace Giftcards\Encryption\Tests\Profile;

use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Tests\AbstractTestCase;

class ProfileTest extends AbstractTestCase
{
    public function testGetters()
    {
        $cipher = $this->getFaker()->unique()->word;
        $keyName = $this->getFaker()->unique()->word;
        $profile = new Profile(
            $cipher,
            $keyName
        );
        $this->assertEquals($cipher, $profile->getCipher());
        $this->assertEquals($keyName, $profile->getKeyName());
        $this->assertTrue($profile->equals(new Profile($cipher, $keyName)));
        $this->assertFalse($profile->equals(new Profile($this->getFaker()->unique()->word, $keyName)));
        $this->assertFalse($profile->equals(new Profile($cipher, $this->getFaker()->unique()->word)));
    }
}
