<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 12:45 PM
 */

namespace Omni\Encryption\Key;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareChainSource extends ChainSource
{
    protected $container;
    protected $serviceIds = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addServiceId($serviceId)
    {
        $this->serviceIds[] = $serviceId;
        return $this;
    }
    
    public function has($key)
    {
        $this->loadServices();
        return parent::has($key);
    }

    public function get($key)
    {
        $this->loadServices();
        return parent::get($key);
    }

    protected function loadServices()
    {
        $this->sources = array_merge(
            $this->sources,
            array_map(array($this->container, 'get'), $this->serviceIds)
        );
        $this->serviceIds = array();
    }
}
