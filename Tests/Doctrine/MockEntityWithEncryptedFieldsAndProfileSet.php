<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 6/4/15
 * Time: 7:22 PM
 */

namespace Giftcards\Encryption\Tests\Doctrine;

use Giftcards\Encryption\Doctrine\Configuration\Annotation\Encrypted;

class MockEntityWithEncryptedFieldsAndProfileSet
{
    public $normalField;
    /**
     * @Encrypted(profile="foo")
     */
    public $encryptedField;
}
