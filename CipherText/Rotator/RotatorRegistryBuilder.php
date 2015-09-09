<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 9:06 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Omni\Encryption\CipherText\Rotator\Factory\DatabaseTableRotatorBuilder;
use Omni\Encryption\CipherText\Rotator\Factory\DoctrineDBALRotatorBuilder;
use Omni\Encryption\Factory\Factory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RotatorRegistryBuilder
{
    /** @var  Factory */
    protected $factory;
    /** @var  RotatorInterface[] */
    protected $rotators = array();

    public static function newInstance()
    {
        return new static(new Factory(array(
            new DatabaseTableRotatorBuilder(),
            new DoctrineDBALRotatorBuilder(),
        )));
    }

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function build()
    {
        $rotatorRegistry = new RotatorRegistry();

        foreach ($this->rotators as $name => $rotator) {
            $rotatorRegistry->set($name, $rotator);
        }

        return $rotatorRegistry;
    }

    public function set($name, $rotator, array $options = array())
    {
        if (!$rotator instanceof RotatorInterface) {
            $rotator = $this->factory->create($rotator, $options);
        }

        $this->rotators[$name] = $rotator;
        return $this;
    }

    public function getFactory()
    {
        return $this->factory;
    }
}
