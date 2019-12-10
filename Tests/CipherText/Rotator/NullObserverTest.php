<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:01 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\NullObserver;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class NullObserverTest extends AbstractExtendableTestCase
{
    /** @var  NullObserver */
    protected $observer;

    public function setUp() : void
    {
        $this->observer = new NullObserver();
    }

    public function testRotateCallbacks()
    {
        $this->expectNoException();
        $this->observer->rotating('');
        $this->observer->rotated('');
    }
}
