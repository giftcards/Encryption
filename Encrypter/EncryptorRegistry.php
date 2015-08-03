<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:45 PM
 */

namespace Omni\Encryption\Encrypter;

class EncryptorRegistry
{
    /** @var EncryptorInterface[] */
    protected $encryptors = array();

    public function add(EncryptorInterface $encryptor)
    {
        $this->encryptors[$encryptor->getName()] = $encryptor;
        return $this;
    }

    public function has($name)
    {
        return isset($this->encryptors[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new EncryptorNotFoundException($name);
        }
        
        return $this->encryptors[$name];
    }

    public function all()
    {
        return $this->encryptors;
    }
}
