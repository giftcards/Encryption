<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 2/5/18
 * Time: 5:50 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareStoreRegistry extends StoreRegistry
{
    protected $container;

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
        $this->stores[$name] = $serviceId;
        return $this;
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    protected function load($name)
    {
        if (!isset($this->stores[$name]) || $this->stores[$name] instanceof StoreInterface) {
            return;
        }

        $this->stores[$name] = $this->container->get($this->stores[$name]);
    }

    public function all()
    {
        foreach ($this->stores as $name => $store) {
            $this->load($name);
        }

        return parent::all();
    }
}