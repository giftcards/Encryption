<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:16 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Omni\Encryption\CipherText\Serializer\ContainerAwareChainDeserializer;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareChainDeserializerTest extends ChainDeserializerTest
{
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
        $this->container->set('serializer2', $this->deserializer2);
        $this->chain = new ContainerAwareChainDeserializer($this->container);
        $this->chain
            ->add($this->deserializer1)
            ->addServiceId('serializer2')
            ->add($this->deserializer3)
        ;
    }
}
