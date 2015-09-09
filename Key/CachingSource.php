<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 2:09 PM
 */

namespace Omni\Encryption\Key;

use Doctrine\Common\Cache\Cache;

class CachingSource implements SourceInterface
{
    protected $inner;
    protected $cache;

    public function __construct(Cache $cache, SourceInterface $inner)
    {
        $this->cache = $cache;
        $this->inner = $inner;
    }

    public function has($key)
    {
        if ($this->cache->contains($key)) {
            return true;
        }
        
        return $this->inner->has($key);
    }

    public function get($key)
    {
        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }
        
        $value = $this->inner->get($key);
        $this->cache->save($key, $value);
        return $value;
    }
}
