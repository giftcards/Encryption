<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:32 PM
 */

namespace Giftcards\Encryption\Tests\Cipher;

use Giftcards\Encryption\Cipher\MysqlAes;
use Giftcards\Encryption\Tests\AbstractTestCase;
use PHPUnit_Framework_TestCase;

class MysqlAesEncryptorTest extends AbstractTestCase
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
        set_error_handler(function () {
            return func_get_arg(1) === 'Function mcrypt_get_iv_size() is deprecated';
        });
        $encrypted = $this->encryptor->encipher($message, $key);

        $decrypted = $this->encryptor->decipher($encrypted, $key);
        $this->assertEquals($message, $decrypted);
        $this->assertNotEquals($message, $encrypted);
        $this->assertNull($this->encryptor->encipher(null, $key));
        $this->assertNull($this->encryptor->decipher(null, $key));
        $this->assertEquals('', $this->encryptor->decipher('', $key));
        $this->assertEquals(' ', $this->encryptor->decipher(' ', $key));
        restore_error_handler();
    }
}
