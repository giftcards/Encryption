<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:32 PM
 */

namespace Omni\Encryption\Tests;

use Omni\Encryption\Encryptor\MysqlAesEncryptor;
use PHPUnit_Framework_TestCase;

class TestEncryptionUtility extends AbstExt
{
    public function testEncryptionAndDecryption()
    {
        $key = "This=1trulySecr3tKey!";
        $encryptor = new MysqlAesEncryptor();
        $message = "My computer beats me at chess, but I sure beat it at kickboxing.";
        $encrypted = $encryptor->encrypt($message, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        $this->assertEquals($message, $decrypted);
        $this->assertNotEquals($message, $encrypted);
    }
}