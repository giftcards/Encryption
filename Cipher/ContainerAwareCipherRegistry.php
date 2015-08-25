<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:19 PM
 */

namespace Omni\Encryption\Cipher;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareCipherRegistry extends CipherRegistry
{
    protected $container;

    /**
     * ContainerAwareEncryptorRegistry constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setServiceId($name, $serviceId)
    {
        $this->ciphers[$name] = $serviceId;
        return $this;
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    public function all()
    {
        foreach ($this->ciphers as $name => $cipher) {
            $this->load($name);
        }

        return parent::all();
    }

    protected function load($name)
    {
        if (!isset($this->ciphers[$name]) || $this->ciphers[$name] instanceof CipherInterface) {
            return;
        }
        
        $this->ciphers[$name] = $this->container->get($this->ciphers[$name]);
    }
}
