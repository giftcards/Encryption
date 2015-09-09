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

    public function addSerializerServiceId($serviceId, $priority = 0)
    {
        $this->sortedSerializers = false;
        if (!isset($this->serializers[$priority])) {
            $this->serializers[$priority] = array();
        }

        $this->serializers[$priority][] = $serviceId;
        return $this;
    }

    public function addDeserializerServiceId($serviceId, $priority = 0)
    {
        $this->sortedDeserializers = false;
        if (!isset($this->deserializers[$priority])) {
            $this->deserializers[$priority] = array();
        }

        $this->deserializers[$priority][] = $serviceId;
        return $this;
    }

    protected function sortSerializers()
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
        parent::sortSerializers();
    }
    
    protected function sortDeserializers()
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
        parent::sortDeserializers();
    }
}
