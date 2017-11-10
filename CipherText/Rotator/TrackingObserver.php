<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:06 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class TrackingObserver implements RotatorObserverInterface
{

    /**
     * @var TrackerInterface
     */
    private $tracker;
    /**
     * @var
     */
    private $storeName;

    public function __construct(TrackerInterface $tracker, $storeName)
    {
        $this->tracker = $tracker;
        $this->storeName = $storeName;
    }

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
        $this->tracker->save($this->storeName, $offset + count($records));
    }
}