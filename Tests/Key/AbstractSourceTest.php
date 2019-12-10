<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:23 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Giftcards\Encryption\Key\SourceInterface;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

abstract class AbstractSourceTest extends AbstractExtendableTestCase
{
    /**
     * @dataProvider gettersHassersProvider
     */
    public function testGettersAndHassers(SourceInterface $source, array $existing, array $notExisting)
    {
        foreach ($existing as $keyName => $key) {
            $this->assertTrue($source->has($keyName));
            $this->assertEquals($key, $source->get($keyName));

        }

        foreach ($notExisting as $keyName) {
            $this->assertFalse($source->has($keyName));
        }
    }

    /**
     * @dataProvider gettersHassersProvider
     */
    public function testGetWhenNonExistent(SourceInterface $source, array $_, array $notExisting)
    {
        $this->expectException('\Giftcards\Encryption\Key\KeyNotFoundException');
        $source->get($notExisting[0]);
    }
    
    abstract public function gettersHassersProvider();
}
