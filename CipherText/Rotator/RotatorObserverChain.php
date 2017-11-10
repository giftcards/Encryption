<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:21 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class RotatorObserverChain implements RotatorObserverInterface
{

    /**
     * @var RotatorObserverInterface[]
     */
    private $observers;

    public function __construct(RotatorObserverInterface ... $observers)
    {
        $this->observers = $observers;
    }


    public function fetchedRecords($offset, $limit, array $records)
    {
        foreach ($this->observers as $observer) {
            $observer->fetchedRecords($offset, $limit, $records);
        }
    }

    public function rotatingRecord(Record $record)
    {
        foreach ($this->observers as $observer) {
            $observer->rotatingRecord($record);
        }
    }

    public function rotatedRecord(Record $record)
    {
        foreach ($this->observers as $observer) {
            $observer->rotatedRecord($record);
        }
    }

    public function savedRecords($offset, $limit, array $records)
    {
        foreach ($this->observers as $observer) {
            $observer->savedRecords($offset, $limit, $records);
        }
    }
}