<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 7:11 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

interface ObserverInterface
{
    public function rotating($id);
    public function rotated($id);
}
