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
    private $observers = array();

    /**
     * RotatorObserverChain constructor.
     * @param RotatorObserverInterface $observers,...
     */
    public function __construct()
    {
        foreach (func_get_args() as $observer) {
            $this->addObserver($observer);
        }
    }

    public function addObserver(RotatorObserverInterface $observer)
    {
        $this->observers[] = $observer;
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
