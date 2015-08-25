<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 7:13 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

class NullObserver implements ObserverInterface
{
    public function rotating($id)
    {
    }

    public function rotated($id)
    {
    }
}
