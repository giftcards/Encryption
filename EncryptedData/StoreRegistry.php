<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:17 PM
 */

namespace Omni\Encryption\EncryptedData;

class StoreRegistry
{
    /**
     * @var StoreInterface[]
     */
    protected $stores = array();

    /**
     * @param StoreInterface $store
     * @return $this
     */
    public function add(StoreInterface $store)
    {
        $this->stores[$store->getName()] = $store;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->stores[$name]);
    }

    /**
     * @param $name
     * @return StoreInterface
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new StoreNotFoundException($name);
        }
        
        return $this->stores[$name];
    }

    /**
     * @return StoreInterface[]
     */
    public function all()
    {
        return $this->stores;
    }
}
