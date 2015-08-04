<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 12:45 PM
 */

namespace Omni\Encryption\Key;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareChainSource implements SourceInterface
{
    protected $container;
    protected $sources = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function add($name, $serviceId)
    {
        $this->sources[$name] = $serviceId;
        return $this;
    }
    
    public function has($key)
    {
        return
            isset($this->sources[$key])
            && ($this->sources[$key] instanceof SourceInterface || $this->container->has($this->sources[$key]))
        ;
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            throw new KeyNotFoundException($key);
        }

        return $this->container->get($this->sources[$key]);
    }
}
