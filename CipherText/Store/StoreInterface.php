<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:15 PM
 */

namespace Omni\Encryption\CipherText\Store;

use Omni\Encryption\Encryptor;

interface StoreInterface
{
    public function rotate(Encryptor $encryptor, $newProfile = null);
}
