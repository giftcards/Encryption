<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:32 PM
 */

namespace Omni\Encryption\Tests\Cipher;

use Omni\Encryption\Cipher\MysqlAes;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use PHPUnit_Framework_TestCase;

class MysqlAesEncryptorTest extends AbstractExtendableTestCase
{
    /** @var  MysqlAes */
    protected $encryptor;

    public function setUp()
    {
        $this->encryptor = new MysqlAes();
    }

    public function testGetName()
    {
        $this->assertEquals('mysql_aes', $this->encryptor->getName());
    }

    public function testEncryptionAndDecryption()
    {
        $key = "This=1trulySecr3tKey!";
        $message = "My computer beats me at chess, but I sure beat it at kickboxing.";
        $encrypted = $this->encryptor->encipher($message, $key);
        $decrypted = $this->encryptor->decipher($encrypted, $key);
        $this->assertEquals($message, $decrypted);
        $this->assertNotEquals($message, $encrypted);
        $this->assertNull($this->encryptor->encipher(null, $key));
        $this->assertNull($this->encryptor->decipher(null, $key));
        $this->assertEquals('', $this->encryptor->decipher('', $key));
        $this->assertEquals(' ', $this->encryptor->decipher(' ', $key));
    }
}
