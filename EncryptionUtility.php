<?php
/**
 * Created by IntelliJ IDEA.
 * User: mnutter
 * Date: 12/4/14
 * Time: 1:26 PM
 */

namespace Omni\Encryption;

class EncryptionUtility
{

    protected $encryptionString;

    /**
     * @param string $encryptionString
     */
    public function __construct($encryptionString)
    {
        $this->encryptionString = $encryptionString;
    }

    /**
     * Takes in a value and then returns the AES Encrypted value
     *
     * @param $value
     * @return null|string
     */
    public function aesEncrypt($value)
    {

        $value = $this->stringify($value);

        if ($value === null) {
            return null;
        }

        $pad_value = 16 - (strlen($value) % 16);
        $value = str_pad($value, (16 * (floor(strlen($value) / 16) + 1)), chr($pad_value));
        return mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($this->encryptionString),
            $value,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
    }

    /**
     * Takes in an AES encrypted value and returns the decrypted value
     *
     * @param $value
     * @return null|string
     */
    public function aesDecrypt($value)
    {

        $value = $this->stringify($value);

        if ($value === null) {
            return null;
        }

        if (strlen(trim($value)) < 1) {
            return $value;
        }

        $value = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $this->mysqlAesKey($this->encryptionString),
            $value,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)
        );
        return rtrim($value, "\x00..\x1F");
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

    /**
     * Used to return the string version of the stream
     *
     * @param $value
     * @return string
     */
    protected function stringify($value)
    {
        if (!is_resource($value)) {
            return $value;
        }
        return stream_get_contents($value);
    }

    /**
     * Sets the internal encryption string to the passed in value
     * @param string $encryptionString The new encryption string to use
     **/
    public function setEncryptionString($encryptionString)
    {
        $this->encryptionString = $encryptionString;
    }
}