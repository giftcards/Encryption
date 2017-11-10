<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 9:09 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;


use Giftcards\Encryption\CipherText\Rotator\StoreInterface;
use Giftcards\Encryption\CipherText\Rotator\StoreRegistry;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Mockery;

class StoreRegistryTest extends AbstractTestCase
{
    public function testRegistry()
    {
        $storeName = $this->getFaker()->unique()->word;
        $store = Mockery::mock(StoreInterface::class);
        $registry = new StoreRegistry();
        $registry->set($storeName, $store);
        $this->assertEquals($store, $registry->get($storeName));
    }
}