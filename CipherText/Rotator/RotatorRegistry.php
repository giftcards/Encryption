<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:17 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

class RotatorRegistry
{
    /**
     * @var RotatorInterface[]
     */
    protected $rotators = array();

    /**
     * @param $name
     * @param RotatorInterface $store
     * @return $this
     */
    public function set($name, RotatorInterface $store)
    {
        $this->rotators[$name] = $store;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->rotators[$name]);
    }

    /**
     * @param $name
     * @return RotatorInterface
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new RotatorNotFoundException($name);
        }
        
        return $this->rotators[$name];
    }

    /**
     * @return RotatorInterface[]
     */
    public function all()
    {
        return $this->rotators;
    }
}
