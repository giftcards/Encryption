<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:17 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator;


interface StoreInterface
{
    /**
     * @param int $offset
     * @param int $limit
     * @return Record[]
     * @throws StoreException
     */
    public function fetch(int $offset, int $limit):array;

    /**
     * @param Record[] $rotatedRecords
     * @return void
     * @throws StoreException
     */
    public function save(array $rotatedRecords);
}