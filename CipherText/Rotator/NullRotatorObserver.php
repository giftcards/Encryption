<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:33 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class NullRotatorObserver implements RotatorObserverInterface
{

    public function fetchedRecords($offset, $limit, array $records)
    {
    }

    public function rotatingRecord(Record $record)
    {
    }

    public function rotatedRecord(Record $record)
    {
    }

    public function savedRecords($offset, $limit, array $records)
    {
    }
}
