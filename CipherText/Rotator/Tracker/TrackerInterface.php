<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:04 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Tracker;

interface TrackerInterface
{
    /**
     * Logs the last offset rotated within this store
     * @param string $storeName
     * @param int $offset
     * @return void
     */
    public function save($storeName, $offset);

    /**
     * @param string $storeName
     * @return int Last offset rotated within the given store
     */
    public function get($storeName);

    /**
     * Resets the last offset rotated in this store
     * @param string $storeName
     * @return void
     */
    public function reset($storeName);
}
