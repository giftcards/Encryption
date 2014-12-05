<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:32 PM
 */

namespace Omni\Encryption\Tests;

use PHPUnit_Framework_TestCase;
use Omni\Encryption\EncryptionUtility;

class TestEncryptionUtility extends PHPUnit_Framework_TestCase
{
    public function testEncryptionAndDecryption()
    {
        $encryptionUtility = new EncryptionUtility("This=1trulySecr3tKey!");
        $message = "My computer beats me at chess, but I sure beat it at kickboxing.";
        $encrypted = $encryptionUtility->aesEncrypt($message);
        $decrypted = $encryptionUtility->aesDecrypt($encrypted);
        $this->assertEquals($message, $decrypted);
        $this->assertNotEquals($message, $encrypted);
    }
}