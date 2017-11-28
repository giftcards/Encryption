<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 3:06 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Tracker;

use Giftcards\Encryption\CipherText\Rotator\NullRotatorObserver;

class TrackingObserver extends NullRotatorObserver
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

    public function savedRecords($offset, $limit, array $records)
    {
        $this->tracker->save($this->storeName, $offset + count($records));
    }
}
