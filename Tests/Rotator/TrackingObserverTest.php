<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/13/17
 * Time: 9:42 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Record;
use Giftcards\Encryption\CipherText\Rotator\TrackerInterface;
use Giftcards\Encryption\CipherText\Rotator\TrackingObserver;
use Giftcards\Encryption\Tests\AbstractTestCase;

class TrackingObserverTest extends AbstractTestCase
{
    public function testTrackingObserver()
    {
        $storeName = $this->getFaker()->unique()->word();

        $tracker = \Mockery::mock(TrackerInterface::class);
        $tracker->shouldReceive("save");
        assert($tracker instanceof TrackerInterface);

        $offset = $this->getFaker()->unique()->randomNumber();
        $limit = $this->getFaker()->unique()->randomNumber();
        $records = [
            new Record(1, []),
            new Record(2, []),
            new Record(3, []),
            new Record(4, []),
        ];

        $observer = new TrackingObserver($tracker, $storeName);
        $observer->savedRecords($offset, $limit, $records);

        $tracker->shouldHaveReceived("save")->withArgs([
            $storeName,
            $offset + count($records)
        ]);
    }
}
