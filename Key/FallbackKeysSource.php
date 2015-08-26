<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:24 PM
 */

namespace Omni\Encryption\Key;

class FallbackKeysSource implements SourceInterface
{
    protected $fallbackKeys;
    protected $inner;

    /**
     * FallbackKeysSource constructor.
     * @param array $fallbackKeys
     * @param SourceInterface $inner
     */
    public function __construct(array $fallbackKeys, SourceInterface $inner)
    {
        $this->fallbackKeys = $fallbackKeys;
        $this->inner = $inner;
    }

    public function has($key)
    {
        foreach ($this->getKeys($key) as $key) {
            if ($this->inner->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function get($key)
    {
        foreach ($this->getKeys($key) as $key) {
            if ($this->inner->has($key)) {
                return $this->inner->get($key);
            }
        }

        throw new KeyNotFoundException($key);
    }

    /**
     * @param $key
     * @return array
     */
    protected function getKeys($key)
    {
        $keys = isset($this->fallbackKeys[$key]) ? $this->fallbackKeys[$key] : array();
        array_unshift($keys, $key);
        return $keys;
    }
}
