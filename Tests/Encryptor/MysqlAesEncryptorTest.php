<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:32 PM
 */

namespace Omni\Encryption\Tests\Encryptor;

use Omni\Encryption\Encryptor\MysqlAesEncryptor;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use PHPUnit_Framework_TestCase;

class MysqlAesEncryptorTest extends AbstractExtendableTestCase
{
    /** @var  MysqlAesEncryptor */
    protected $encryptor;

    public function setUp()
    {
        $this->encryptor = new MysqlAesEncryptor();
    }

    public function testGetName()
    {
        $this->assertEquals('mysql_aes', $this->encryptor->getName());
    }

    public function testEncryptionAndDecryption()
    {
        $key = "This=1trulySecr3tKey!";
        $message = "My computer beats me at chess, but I sure beat it at kickboxing.";
        $encrypted = $this->encryptor->encrypt($message, $key);
        $decrypted = $this->encryptor->decrypt($encrypted, $key);
        $this->assertEquals($message, $decrypted);
        $this->assertNotEquals($message, $encrypted);
        $this->assertNull($this->encryptor->encrypt(null, $key));
        $this->assertNull($this->encryptor->decrypt(null, $key));
        $this->assertEquals('', $this->encryptor->decrypt('', $key));
        $this->assertEquals(' ', $this->encryptor->decrypt(' ', $key));
    }
}
