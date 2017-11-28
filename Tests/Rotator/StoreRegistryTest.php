<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 9:09 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Mockery;

class StoreRegistryTest extends AbstractTestCase
{
    public function testRegistry()
    {
        $storeName = $this->getFaker()->unique()->word;
        $store = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface");
        $registry = new StoreRegistry();
        $registry->set($storeName, $store);
        $this->assertSame($store, $registry->get($storeName));
    }
}
