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

    public function __construct(SourceInterface $inner, Cache $cache)
    {
        $this->inner = $inner;
        $this->cache = $cache;
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
        
        $key = $this->inner->get($key);
        $this->cache->save($key);
        return $key;
    }
}
