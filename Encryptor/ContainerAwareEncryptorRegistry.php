<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/6/15
 * Time: 3:19 PM
 */

namespace Omni\Encryption\Encryptor;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareEncryptorRegistry extends EncryptorRegistry
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

    public function add(EncryptorInterface $encryptor)
    {
        unset($this->serviceIds[$encryptor->getName()]);
        return parent::add($encryptor);
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
        if (isset($this->encryptors[$name]) || !isset($this->serviceIds[$name])) {
            return;
        }
        
        $this->encryptors[$name] = $this->container->get($this->serviceIds[$name]);
    }
}
