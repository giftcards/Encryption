<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 12:45 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareChainSerializerDeserializer extends ChainSerializerDeserializer
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addServiceId($serviceId, $priority = 0)
    {
        $this->sorted = false;
        if (!isset($this->serializers[$priority])) {
            $this->serializers[$priority] = array();
        }

        $this->serializers[$priority][] = $serviceId;
        return $this;
    }
    
    protected function sort()
    {
        $container = $this->container;
        $this->serializers = array_map(function (array $serializers) use ($container) {
            return array_map(
                function ($serializer) use ($container) {
                    if (!$serializer instanceof SerializerInterface) {
                        $serializer = $container->get($serializer);
                    }
                    return $serializer;
                },
                $serializers
            );
        }, $this->serializers);
        parent::sort();
    }
}
