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
    private $buildQueue = array();

    /**
     * Constructs a StoreRegistryBuilder with built-in StoreBuilders
     * @param BuilderInterface[] $builders Additional builders
     * @return StoreRegistryBuilder
     */
    public static function factory($builders = array())
    {
        $builders[] = new DoctrineDBALStoreBuilder();
        return new self($builders);
    }

    /**
     * StoreRegistryBuilder constructor.
     * @param array $builders
     */
    public function __construct($builders = array())
    {
        $this->factory = new Factory("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface", $builders);
    }

    /**
     * @param string $storeName
     * @param string|StoreInterface $store A store or a builder name
     * @param array $options Options for the builder
     * @return StoreRegistryBuilder
     * @throws \TypeError
     */
    public function addStore($storeName, $store, array $options = array())
    {
        if ($store instanceof StoreInterface) {
            return $this->addStoreToRegistry($storeName, $store);
        }

        if (is_string($store)) {
            return $this->addStoreToBuildQueue($storeName, $store, $options);
        }

        throw new \InvalidArgumentException(
            sprintf(
                "Second argument for StoreRegistryBuilder::addStore() must be a string or StoreInterface. %s given",
                is_object($store) ? get_class($store) : gettype($store)
            )
        );
    }

    /**
     * @return StoreRegistry
     */
    public function build()
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
    private function addStoreToBuildQueue($storeName, $builderName, array $options)
    {
        if (!$this->factory->hasBuilder($builderName)) {
            throw new \DomainException(sprintf("Unknown builder: %s", $builderName));
        }
        $this->buildQueue[$storeName] = array($builderName, $options);
        return $this;
    }

    /**
     * @param string $storeName
     * @param StoreInterface $store
     * @return StoreRegistryBuilder
     */
    private function addStoreToRegistry($storeName, StoreInterface $store)
    {
        $this->buildQueue[$storeName] = $store;
        return $this;
    }

    /**
     * @param StoreInterface|array $entry A StoreInterface or an array in the format [builderName, options]
     * @return StoreInterface
     */
    private function buildEntry($entry)
    {
        if ($entry instanceof StoreInterface) {
            return $entry;
        }
        list($builderName, $options) = $entry;
        return $this->factory->create($builderName, $options);
    }


}