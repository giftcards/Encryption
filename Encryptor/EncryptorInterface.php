<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:39 PM
 */

namespace Omni\Encryption\Encryptor;

interface EncryptorInterface
{
    public function getName();
    public function encrypt($data, $encryptionKey);
    public function decrypt($data, $encryptionKey);
}
