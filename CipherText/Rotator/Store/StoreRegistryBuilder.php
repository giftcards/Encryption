<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 1:26 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

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
     * @param array $builders Additional builders
     */
    public function __construct($builders = [])
    {
        $builders[] = new DoctrineDBALStoreBuilder();
        $this->factory = new Factory(StoreInterface::class, $builders);
    }

    /**
     * @param $builder
     * @param $storeName
     * @param array $options
     * @return self
     */
    public function addStore($builder, $storeName, array $options)
    {
        $this->stores[] = [
            'builder' => $builder,
            'storeName' => $storeName,
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
        foreach ($this->stores as list($builder, $storeName, $options)) {
            $registry->set($storeName, $this->factory->create($builder, $options));
        }
        return $registry;
    }
}