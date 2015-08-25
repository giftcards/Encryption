<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:15 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

use Omni\Encryption\Encryptor;

interface RotatorInterface
{
    public function rotate(ObserverInterface $observer, Encryptor $encryptor, $newProfile = null);
}
