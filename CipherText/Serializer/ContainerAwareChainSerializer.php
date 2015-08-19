<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 12:45 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareChainSerializer extends ChainSerializer
{
    protected $container;
    protected $serviceIds = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addServiceId($serviceId, $priority = 0)
    {
        $this->sorted = false;
        if (!isset($this->serviceIds[$priority])) {
            $this->serviceIds[$priority] = array();
        }

        $this->serviceIds[$priority][] = $serviceId;
        return $this;
    }
    
    protected function sort()
    {
        foreach ($this->serviceIds as $priority => $serviceIds) {
            $this->serializers[$priority] = array_merge(
                isset($this->serializers[$priority]) ? $this->serializers[$priority] : array(),
                array_map(array($this->container, 'get'), $serviceIds)
            );
        }
        $this->serviceIds = array();
        return parent::sort();
    }
}
