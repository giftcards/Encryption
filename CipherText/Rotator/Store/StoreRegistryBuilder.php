<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 1:26 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Factory\Factory;

class StoreRegistryBuilder
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var array
     */
    private $buildQueue = [];

    /**
     * Constructs a StoreRegistryBuilder with built-in StoreBuilders
     * @param BuilderInterface[] $builders Additional builders
     */
    public function __construct($builders = [])
    {
        $builders[] = new DoctrineDBALStoreBuilder();
        $this->factory = new Factory(StoreInterface::class, $builders);
    }

    /**
     * @param string $storeName
     * @param string|StoreInterface $store A store or a builder name
     * @param array $options Options for the builder
     * @return StoreRegistryBuilder
     * @throws \TypeError
     */
    public function addStore(string $storeName, $store, array $options = []): StoreRegistryBuilder
    {
        if ($store instanceof StoreInterface) {
            return $this->addStoreToRegistry($storeName, $store);
        }

        if (is_string($store)) {
            return $this->addStoreToBuildQueue($storeName, $store, $options);
        }

        throw new \TypeError(
            sprintf(
                "Second argument for StoreRegistryBuilder::addStore() must be a string or StoreInterface. %s given",
                is_object($store) ? get_class($store) : gettype($store)
            )
        );
    }

    /**
     * @return StoreRegistry
     */
    public function build(): StoreRegistry
    {
        $registry = new StoreRegistry();
        foreach ($this->buildQueue as $storeName => $entry) {
            $registry->set($storeName, $this->buildEntry($entry));
        }
        return $registry;
    }

    /**
     * Queues a store to be built
     * @param string $storeName
     * @param $builderName
     * @param array $options
     * @return StoreRegistryBuilder
     */
    private function addStoreToBuildQueue(string $storeName, $builderName, array $options): StoreRegistryBuilder
    {
        if (!$this->factory->hasBuilder($builderName)) {
            throw new \DomainException(sprintf("Unknown builder: %s", $builderName));
        }
        $this->buildQueue[$storeName] = [$builderName, $options];
        return $this;
    }

    /**
     * @param string $storeName
     * @param StoreInterface $store
     * @return StoreRegistryBuilder
     */
    private function addStoreToRegistry(string $storeName, StoreInterface $store): StoreRegistryBuilder
    {
        $this->buildQueue[$storeName] = $store;
        return $this;
    }

    /**
     * @param StoreInterface|array $entry A StoreInterface or an array in the format [builderName, options]
     * @return StoreInterface
     */
    private function buildEntry($entry): StoreInterface
    {
        if( $entry instanceof StoreInterface) {
            return $entry;
        }
        list($builderName, $options) = $entry;
        return $this->factory->create($builderName, $options);
    }


}