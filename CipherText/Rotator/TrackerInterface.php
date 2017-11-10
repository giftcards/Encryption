<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:04 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;


interface TrackerInterface
{
    public function save($storeName, $offset);
    public function get($storeName):int;
}