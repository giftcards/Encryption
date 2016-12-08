<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 12/8/16
 * Time: 2:42 PM
 */

namespace Giftcards\Encryption\Tests\Doctrine;

use Giftcards\Encryption\Doctrine\FieldData;
use Giftcards\Encryption\Tests\AbstractTestCase;

class FieldDataTest extends AbstractTestCase
{
    public function testGetters()
    {
        $clearText = $this->getFaker()->unique()->word;
        $cipherText = $this->getFaker()->unique()->word;
        $profile = $this->getFaker()->unique()->word;
        $data = new FieldData(
            $clearText,
            $cipherText,
            $profile
        );
        $this->assertEquals($clearText, $data->getClearText());
        $this->assertEquals($cipherText, $data->getCipherText());
        $this->assertEquals($profile, $data->getProfile());
    }
}
