<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:41 PM
 */

namespace Omni\Encryption\Cipher;

class MysqlAes implements CipherInterface
{
    public function getName()
    {
        return 'mysql_aes';
    }
    
    public function encipher($clearText, $key)
    {
        if ($clearText === null) {
            return null;
        }

        $pad_value = 16 - (strlen($clearText) % 16);
        $clearText = str_pad($clearText, (16 * (floor(strlen($clearText) / 16) + 1)), chr($pad_value));
        return mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($key),
            $clearText,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
    }

    public function decipher($cipherText, $key)
    {
        if ($cipherText === null) {
            return null;
        }

        if (strlen(trim($cipherText)) == 0) {
            return $cipherText;
        }

        $cipherText = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($key),
            $cipherText,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
        return rtrim($cipherText, "\x00..\x1F");
    }

    /**
     * Converts the key into the MySQL equivalent version
     *
     * @param $key
     * @return string
     */
    protected function mysqlAesKey($key)
    {
        $new_key = str_repeat(chr(0), 16);

        for ($i = 0, $len = strlen($key); $i < $len; $i++) {
            $new_key[$i % 16] = $new_key[$i % 16] ^ $key[$i];
        }

        return $new_key;
    }
}
