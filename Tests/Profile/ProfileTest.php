<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:45 PM
 */

namespace Omni\Encryption\Tests\Profile;

use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ProfileTest extends AbstractExtendableTestCase
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
