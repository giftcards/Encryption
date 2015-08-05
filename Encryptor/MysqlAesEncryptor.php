<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:41 PM
 */

namespace Omni\Encryption\Encryptor;

class MysqlAesEncryptor implements EncryptorInterface
{
    public function getName()
    {
        return 'mysql_aes';
    }
    
    public function encrypt($data, $encryptionKey)
    {
        if ($data === null) {
            return null;
        }

        $pad_value = 16 - (strlen($data) % 16);
        $data = str_pad($data, (16 * (floor(strlen($data) / 16) + 1)), chr($pad_value));
        return mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($encryptionKey),
            $data,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
    }

    public function decrypt($data, $encryptionKey)
    {
        if ($data === null) {
            return null;
        }

        if (strlen(trim($data)) == 0) {
            return $data;
        }

        $data = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($encryptionKey),
            $data,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
        return rtrim($data, "\x00..\x1F");
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
