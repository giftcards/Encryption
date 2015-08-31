<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 12:45 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareChainDeserializer extends ChainDeserializer
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addServiceId($serviceId, $priority = 0)
    {
        $this->sorted = false;
        if (!isset($this->deserializers[$priority])) {
            $this->deserializers[$priority] = array();
        }

        $this->deserializers[$priority][] = $serviceId;
        return $this;
    }
    
    protected function sort()
    {
        $container = $this->container;
        $this->deserializers = array_map(function (array $serializers) use ($container) {
            return array_map(
                function ($serializer) use ($container) {
                    if (!$serializer instanceof DeserializerInterface) {
                        $serializer = $container->get($serializer);
                    }
                    return $serializer;
                },
                $serializers
            );
        }, $this->deserializers);
        parent::sort();
    }
}
