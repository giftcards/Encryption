<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:39 PM
 */

namespace Giftcards\Encryption\Cipher;

interface CipherInterface
{
    public function getName();
    public function encipher($clearText, $key);
    public function decipher($cipherText, $key);
}
