<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 5:59 PM
 */

namespace Omni\Encryption\Key;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerParametersSource extends AbstractSource
{
    protected $container;

    /**
     * ContainerSource constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function has($key)
    {
        return $this->container->hasParameter($key);
    }

    public function getKey($key)
    {
        return $this->container->getParameter($key);
    }
}
