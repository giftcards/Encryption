<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 7:13 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class NullObserver implements RotatorObserverInterface
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
