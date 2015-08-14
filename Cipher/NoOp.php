<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 10:43 PM
 */

namespace Omni\Encryption\Cipher;

class NoOp implements CipherInterface
{

    public function getName()
    {
        return 'no_op';
    }

    public function encipher($clearText, $key)
    {
        return $clearText;
    }

    public function decipher($cipherText, $key)
    {
        return $cipherText;
    }
}
