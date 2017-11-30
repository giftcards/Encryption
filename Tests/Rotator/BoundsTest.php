<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:37 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\Tests\AbstractTestCase;

class BoundsTest extends AbstractTestCase
{
    public function testIteration()
    {
        $bounds = new Bounds(0, 12, 1);
        $this->assertEquals(array(
            array(0, 1),
            array(1, 1),
            array(2, 1),
            array(3, 1),
            array(4, 1),
            array(5, 1),
            array(6, 1),
            array(7, 1),
            array(8, 1),
            array(9, 1),
            array(10, 1),
            array(11, 1),
        ), iterator_to_array($bounds));
        $bounds = new Bounds(0, 12, 3);
        $this->assertEquals(array(
            array(0, 3),
            array(3, 3),
            array(6, 3),
            array(9, 3)
        ), iterator_to_array($bounds));
        $bounds = new Bounds(2, 12, 3);
        $this->assertEquals(array(
            array(2, 3),
            array(5, 3),
            array(8, 3),
            array(11, 1),
        ), iterator_to_array($bounds));
        $bounds = new Bounds(2, null, 4);
        $this->assertEquals(array(
            array(2, 4),
            array(6, 4),
            array(10, 4),
            array(14, 4),
            array(18, 4),
        ), iterator_to_array(new \LimitIterator($bounds, 0, 5)));
        $this->assertEquals(array(
            1 => array(6, 4),
            2 => array(10, 4),
            3 => array(14, 4),
        ), iterator_to_array(new \LimitIterator($bounds, 1, 3)));
    }
}
