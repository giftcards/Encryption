<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:45 PM
 */

namespace Omni\Encryption\Cipher;

class CipherRegistry
{
    /** @var CipherInterface[] */
    protected $ciphers = array();

    public function add(CipherInterface $cipher)
    {
        $this->ciphers[$cipher->getName()] = $cipher;
        return $this;
    }

    public function has($name)
    {
        return isset($this->ciphers[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new CipherNotFoundException($name);
        }
        
        return $this->ciphers[$name];
    }

    public function all()
    {
        return $this->ciphers;
    }
}
