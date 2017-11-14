<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:17 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use Giftcards\Encryption\CipherText\Rotator\Record;

interface StoreInterface
{
    /**
     * @param int $offset
     * @param int $limit
     * @return Record[]
     */
    public function fetch(int $offset, int $limit):array;

    /**
     * @param Record[] $rotatedRecords
     * @return void
     */
    public function save(array $rotatedRecords);
}