<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 7:16 PM
 */

namespace Omni\Encryption\Tests\CipherText\Serializer;

use Omni\Encryption\CipherText\Serializer\ContainerAwareChainSerializer;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareChainSerializerTest extends ChainSerializerTest
{
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
        $this->container->set('serializer2', $this->serializer2);
        $this->chain = new ContainerAwareChainSerializer($this->container);
        $this->chain
            ->add($this->serializer1)
            ->addServiceId('serializer2')
            ->add($this->serializer3)
        ;
    }
}
