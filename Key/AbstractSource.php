<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:07 PM
 */

namespace Giftcards\Encryption\Key;

abstract class AbstractSource implements SourceInterface
{
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new KeyNotFoundException($key);
        }
        
        return $this->getKey($key);
    }

    abstract protected function getKey($key);
}