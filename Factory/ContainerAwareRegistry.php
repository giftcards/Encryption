<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:19 PM
 */

namespace Giftcards\Encryption\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareRegistry extends Registry
{
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param array $factories
     */
    public function __construct(ContainerInterface $container, array $factories = array())
    {
        $this->container = $container;
        parent::__construct($factories);
    }

    public function setServiceId($name, $serviceId)
    {
        $this->builders[$name] = $serviceId;
        return $this;
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    public function all()
    {
        foreach ($this->builders as $name => $cipher) {
            $this->load($name);
        }

        return parent::all();
    }

    protected function load($name)
    {
        if (!isset($this->builders[$name]) || $this->builders[$name] instanceof BuilderInterface) {
            return;
        }
        
        $this->builders[$name] = $this->container->get($this->builders[$name]);
    }
}
