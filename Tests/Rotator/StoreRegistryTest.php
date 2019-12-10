<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 9:09 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;

use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class StoreRegistryTest extends AbstractExtendableTestCase
{
    /** @var  StoreRegistry */
    protected $registry;

    public function setUp() : void
    {
        $this->registry = new StoreRegistry();
    }

    public function testGettersSetters()
    {
        $store1 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store1Name = $this->getFaker()->unique()->word;
        $store2 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store2Name = $this->getFaker()->unique()->word;
        $store3 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store3Name = $this->getFaker()->unique()->word;

        $this->registry
            ->set($store1Name, $store1)
            ->set($store2Name, $store2)
            ->set($store3Name, $store3)
        ;
        $this->assertTrue($this->registry->has($store1Name));
        $this->assertSame($store1, $this->registry->get($store1Name));
        $this->assertTrue($this->registry->has($store2Name));
        $this->assertSame($store2, $this->registry->get($store2Name));
        $this->assertTrue($this->registry->has($store3Name));
        $this->assertSame($store3, $this->registry->get($store3Name));
        $this->assertSame([
            $store1Name => $store1,
            $store2Name => $store2,
            $store3Name => $store3,
        ], $this->registry->all());
    }

    public function testGetWhereNotThere()
    {
        $this->expectException('\Giftcards\Encryption\CipherText\Rotator\Store\StoreNotFoundException');
        $store1 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store1Name = $this->getFaker()->unique()->word;
        $store2 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store2Name = $this->getFaker()->unique()->word;
        $store3 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store3Name = $this->getFaker()->unique()->word;

        $this->registry
            ->set($store1Name, $store1)
            ->set($store2Name, $store2)
            ->set($store3Name, $store3)
            ->get($this->getFaker()->unique()->word)
        ;
    }
}
