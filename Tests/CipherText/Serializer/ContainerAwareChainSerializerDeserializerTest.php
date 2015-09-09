<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:16 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Serializer;

use Giftcards\Encryption\CipherText\Serializer\ContainerAwareChainSerializerDeserializer;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareChainSerializerDeserializerTest extends ChainSerializerDeserializerTest
{
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
        $this->container->set('deserializer2', $this->deserializer2);
        $this->container->set('serializer2', $this->serializer2);
        $this->chain = new ContainerAwareChainSerializerDeserializer($this->container);
        $this->chain
            ->addSerializer($this->serializer1)
            ->addSerializerServiceId('serializer2')
            ->addSerializer($this->serializer3)
            ->addDeserializer($this->deserializer1)
            ->addDeserializerServiceId('deserializer2')
            ->addDeserializer($this->deserializer3)
        ;
    }
}
