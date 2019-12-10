<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/20/17
 * Time: 3:40 PM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Tracker\NullTracker;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class NullTrackerTest extends AbstractExtendableTestCase
{

    public function testTracker()
    {
        $storeName = $this->getFaker()->unique()->word();

        $tracker = new NullTracker();
        $this->assertEquals(0, $tracker->get($storeName));
        $tracker->reset($storeName);
        $tracker->save($storeName, 0);
    }
}
