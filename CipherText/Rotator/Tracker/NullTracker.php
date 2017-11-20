<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/14/17
 * Time: 1:31 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Tracker;

class NullTracker implements TrackerInterface
{

    /**
     * Logs the last offset rotated within this store
     * @param string $storeName
     * @param int $offset
     * @return void
     */
    public function save($storeName, $offset)
    {
    }

    /**
     * @param string $storeName
     * @return int Last offset rotated within the given store
     */
    public function get($storeName)
    {
        return 0;
    }

    /**
     * Resets the last offset rotated in this store
     * @param string $storeName
     * @return void
     */
    public function reset($storeName)
    {
    }
}