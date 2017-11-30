<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/21/17
 * Time: 1:45 PM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistryBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreRegistryBuilderTest extends AbstractTestCase
{

    public function testAddStore()
    {
        $storeName = $this->getFaker()->unique()->word();
        $store = \Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface");

        $regBuilder = new StoreRegistryBuilder();
        $regBuilder->addStore($storeName, $store);
        $registry = $regBuilder->build();

        $this->assertTrue($registry->has($storeName));
        $this->assertEquals($store, $registry->get($storeName));
    }

    public function testBuilder()
    {
        $storeName = $this->getFaker()->unique()->word();
        $store = \Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface");

        $builderName = $this->getFaker()->unique()->word();
        $builderOptions = array('foo' => 'bar');
        $builder = \Mockery::mock("Giftcards\\Encryption\\Factory\\BuilderInterface");
        $builder->shouldReceive("getName")->andReturn($builderName);
        $builder->shouldReceive("configureOptionsResolver")->andReturnUsing(function (OptionsResolver $resolver) {
            $resolver->setRequired(array("foo"));
        });
        $builder->shouldReceive("build")->with($builderOptions)->andReturn($store);

        $regBuilder = new StoreRegistryBuilder(array($builder));
        $regBuilder->addStore($storeName, $builderName, $builderOptions);
        $registry = $regBuilder->build();

        $this->assertTrue($registry->has($storeName));
        $this->assertEquals($store, $registry->get($storeName));
    }

    public function testFactory()
    {
        $builder = StoreRegistryBuilder::newInstance();
        $registry = $builder->build();
    }

    public function testTypeChecking()
    {
        $storeName = $this->getFaker()->unique()->word();
        $store = $this->getFaker()->unique()->randomNumber();

        try {
            $regBuilder = new StoreRegistryBuilder();
            $regBuilder->addStore($storeName, $store);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof \InvalidArgumentException);
            $this->assertEquals(
                "Second argument for StoreRegistryBuilder::addStore() must be a string or StoreInterface. integer given",
                $exception->getMessage()
            );
        }
    }

    public function testBadBuilder()
    {
        $storeName = $this->getFaker()->unique()->word();
        $builderName = $this->getFaker()->unique()->word();

        try {
            $regBuilder = new StoreRegistryBuilder();
            $regBuilder->addStore($storeName, $builderName);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof \DomainException);
            $this->assertEquals(sprintf("Unknown builder: %s", $builderName), $exception->getMessage());
        }

    }
}
