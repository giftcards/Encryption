<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:37 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use LimitIterator;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class BoundsTest extends AbstractExtendableTestCase
{
    public function testIteration()
    {
        $bounds = new Bounds(0, 12, 1);
        $this->assertEquals([
            [0, 1],
            [1, 1],
            [2, 1],
            [3, 1],
            [4, 1],
            [5, 1],
            [6, 1],
            [7, 1],
            [8, 1],
            [9, 1],
            [10, 1],
            [11, 1],
        ], iterator_to_array($bounds));
        $bounds = new Bounds(0, 12, 3);
        $this->assertEquals([
            [0, 3],
            [3, 3],
            [6, 3],
            [9, 3]
        ], iterator_to_array($bounds));
        $bounds = new Bounds(2, 12, 3);
        $this->assertEquals([
            [2, 3],
            [5, 3],
            [8, 3],
            [11, 1],
        ], iterator_to_array($bounds));
        $bounds = new Bounds(2, null, 4);
        $this->assertEquals([
            [2, 4],
            [6, 4],
            [10, 4],
            [14, 4],
            [18, 4],
        ], iterator_to_array(new LimitIterator($bounds, 0, 5)));
        $this->assertEquals([
            1 => [6, 4],
            2 => [10, 4],
            3 => [14, 4],
        ], iterator_to_array(new LimitIterator($bounds, 1, 3)));
    }
}
