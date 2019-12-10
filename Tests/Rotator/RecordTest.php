<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 8:40 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Record;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class RecordTest extends AbstractExtendableTestCase
{
    public function testRecord()
    {
        $record = new Record(1, ['foo' => 'bar']);
        $this->assertEquals(1, $record->getId());
        $this->assertEquals(['foo' => 'bar'], $record->getData());
    }
}
