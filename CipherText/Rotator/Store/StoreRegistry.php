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
     * @var StoreInterface[]
     */
    protected $stores = array();

    /**
     * @param string $storeName
     * @param StoreInterface $store
     * @return $this
     */
    public function set($storeName, StoreInterface $store)
    {
        $this->stores[$storeName] = $store;
        return $this;
    }

    /**
     * @param $storeName
     * @return StoreInterface
     */
    public function get($storeName)
    {
        if (!$this->has($storeName)) {
            throw new StoreNotFoundException($storeName);
        }

        return $this->stores[$storeName];
    }

    public function has($storeName)
    {
        return isset($this->stores[$storeName]);
    }

    /**
     * @return StoreInterface[]
     */
    public function all()
    {
        return $this->stores;
    }
}
