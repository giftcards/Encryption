<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 2/5/18
 * Time: 6:07 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator\Store;

use Giftcards\Encryption\CipherText\Rotator\Store\ContainerAwareStoreRegistry;

use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareStoreRegistryTest extends AbstractExtendableTestCase
{
    /** @var  Container */
    protected $container;
    /** @var  ContainerAwareStoreRegistry */
    protected $registry;

    public function setUp() : void
    {
        $this->registry = new ContainerAwareStoreRegistry(
            $this->container = new Container()
        );
    }

    public function testGettersSetters()
    {
        $store1 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store1Name = $this->getFaker()->unique()->word;
        $store2 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store2Name = $this->getFaker()->unique()->word;
        $store3 = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface');
        $store3Name = $this->getFaker()->unique()->word;

        $this->container->set('store2', $store2);

        $this->registry
            ->set($store1Name, $store1)
            ->setServiceId($store2Name, 'store2')
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
        $this->container->set('store2', $store2);

        $this->registry
            ->set($store1Name, $store1)
            ->setServiceId($store2Name, 'store2')
            ->set($store3Name, $store3)
            ->get($this->getFaker()->unique()->word)
        ;
    }
}
