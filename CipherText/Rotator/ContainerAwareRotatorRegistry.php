<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:19 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareRotatorRegistry extends RotatorRegistry
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
        $this->rotators[$name] = $serviceId;
        return $this;
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    public function all()
    {
        foreach ($this->rotators as $name => $rotator) {
            $this->load($name);
        }

        return parent::all();
    }

    protected function load($name)
    {
        if (!isset($this->rotators[$name]) || $this->rotators[$name] instanceof RotatorInterface) {
            return;
        }
        
        $this->rotators[$name] = $this->container->get($this->rotators[$name]);
    }
}
