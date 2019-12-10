<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/21/17
 * Time: 1:45 PM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistryBuilder;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreRegistryBuilderTest extends AbstractExtendableTestCase
{

    public function testAddStore()
    {
        $storeName = $this->getFaker()->unique()->word();
        $store = Mockery::mock("Giftcards\Encryption\CipherText\Rotator\Store\StoreInterface");

        $regBuilder = new StoreRegistryBuilder();
        $regBuilder->addStore($storeName, $store);
        $registry = $regBuilder->build();

        $this->assertTrue($registry->has($storeName));
        $this->assertEquals($store, $registry->get($storeName));
    }

    public function testBuilder()
    {
        $storeName = $this->getFaker()->unique()->word();
        $store = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface");

        $builderName = $this->getFaker()->unique()->word();
        $builderOptions = ['foo' => 'bar'];
        $builder = Mockery::mock("Giftcards\\Encryption\\Factory\\BuilderInterface");
        $builder->shouldReceive("getName")->andReturn($builderName);
        $builder->shouldReceive("configureOptionsResolver")->andReturnUsing(function (OptionsResolver $resolver) {
            $resolver->setRequired(["foo"]);
        });
        $builder->shouldReceive("build")->with($builderOptions)->andReturn($store);

        $regBuilder = new StoreRegistryBuilder([$builder]);
        $regBuilder->addStore($storeName, $builderName, $builderOptions);
        $registry = $regBuilder->build();

        $this->assertTrue($registry->has($storeName));
        $this->assertEquals($store, $registry->get($storeName));
    }

    public function testFactory()
    {
        $this->expectNoException();
        $builder = StoreRegistryBuilder::newInstance();
        $builder->build();
    }

    public function testTypeChecking()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage("Second argument for StoreRegistryBuilder::addStore() must be a string or StoreInterface. integer given");
        $storeName = $this->getFaker()->unique()->word();
        $store = $this->getFaker()->unique()->randomNumber();
        $regBuilder = new StoreRegistryBuilder();
        $regBuilder->addStore($storeName, $store);
    }

    public function testBadBuilder()
    {
        $storeName = $this->getFaker()->unique()->word();
        $builderName = $this->getFaker()->unique()->word();
        $this->expectException('\DomainException');
        $this->expectExceptionMessage('Unknown builder: ' . $builderName);
        $regBuilder = new StoreRegistryBuilder();
        $regBuilder->addStore($storeName, $builderName);
    }
}
