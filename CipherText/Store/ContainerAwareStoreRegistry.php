<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:19 PM
 */

namespace Omni\Encryption\CipherText\Store;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareStoreRegistry extends StoreRegistry
{
    protected $container;
    protected $serviceIds = array();

    /**
     * ContainerAwareStoreRegistry constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setServiceId($name, $serviceId)
    {
        $this->serviceIds[$name] = $serviceId;
        return $this;
    }

    public function set($name, StoreInterface $store)
    {
        unset($this->serviceIds[$name]);
        return parent::set($name, $store);
    }

    public function has($name)
    {
        if (isset($this->serviceIds[$name])) {
            return true;
        }
        
        return parent::has($name);
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    public function all()
    {
        foreach ($this->serviceIds as $name => $serviceId) {
            $this->load($name);
        }

        return parent::all();
    }

    protected function load($name)
    {
        if (isset($this->stores[$name]) || !isset($this->serviceIds[$name])) {
            return;
        }
        
        $this->stores[$name] = $this->container->get($this->serviceIds[$name]);
    }
}
