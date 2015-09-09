<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:01 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\NullObserver;
use Giftcards\Encryption\Tests\AbstractTestCase;

class NullObserverTest extends AbstractTestCase
{
    /** @var  NullObserver */
    protected $observer;

    public function setUp()
    {
        $this->observer = new NullObserver();
    }

    public function testRotateCallbacks()
    {
        $this->observer->rotating('');
        $this->observer->rotated('');
    }
}
