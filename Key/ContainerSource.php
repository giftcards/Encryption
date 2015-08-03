<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 5:59 PM
 */

namespace Omni\Encryption\Key;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerSource extends AbstractSource
{
    protected $container;
    protected $parameterMap;

    /**
     * ContainerSource constructor.
     * @param ContainerInterface $container
     * @param array $parameterMap
     */
    public function __construct(ContainerInterface $container, array $parameterMap)
    {
        $this->container = $container;
        $this->parameterMap = $parameterMap;
    }

    public function has($key)
    {
        return
            isset($this->parameterMap[$key])
            && $this->container->hasParameter($this->parameterMap[$key])
        ;
    }

    public function getKey($key)
    {
        return $this->container->getParameter($this->parameterMap[$key]);
    }
}