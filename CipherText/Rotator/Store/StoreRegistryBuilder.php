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
    private $stores = [];
    
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
     * @param $storeName
     * @param $builderName
     * @param array $options
     * @return StoreRegistryBuilder
     */
    public function addStore($storeName, $builderName, array $options = []): StoreRegistryBuilder
    {
        $this->stores[$storeName] = [
            'builder' => $builderName,
            'options' => $options
        ];
        return $this;
    }

    /**
     * @return StoreRegistry
     */
    public function build(): StoreRegistry
    {
        $registry = new StoreRegistry();
        foreach ($this->stores as $storeName => list($builderName, $options)) {
            $registry->set($storeName, $this->factory->create($builderName, $options));
        }
        return $registry;
    }
}