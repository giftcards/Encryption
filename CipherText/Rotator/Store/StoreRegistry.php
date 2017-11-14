<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:16 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

class StoreRegistry
{

    /**
     * @var StoreInterface
     */
    private $stores = [];

    /**
     * @param string $storeName
     * @param StoreInterface $store
     * @return $this
     */
    public function set(string $storeName, StoreInterface $store)
    {
        $this->stores[$storeName] = $store;
        return $this;
    }

    /**
     * @param $storeName
     * @return StoreInterface
     */
    public function get($storeName):StoreInterface
    {
        return $this->stores[$storeName];
    }

}