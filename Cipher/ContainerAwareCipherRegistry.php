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
    protected $serviceIds = array();

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
        $this->serviceIds[$name] = $serviceId;
        return $this;
    }

    public function add(CipherInterface $cipher)
    {
        unset($this->serviceIds[$cipher->getName()]);
        return parent::add($cipher);
    }

    public function has($name)
    {
        if (isset($this->serviceIds[$name])) {
            return true;
        }
        
        return parent::has($name);
    }

    public function get($name)
    {
        $this->load($name);
        return parent::get($name);
    }

    public function all()
    {
        foreach ($this->serviceIds as $name => $serviceId) {
            $this->load($name);
        }

        return parent::all();
    }

    protected function load($name)
    {
        if (isset($this->ciphers[$name]) || !isset($this->serviceIds[$name])) {
            return;
        }
        
        $this->ciphers[$name] = $this->container->get($this->serviceIds[$name]);
    }
}
