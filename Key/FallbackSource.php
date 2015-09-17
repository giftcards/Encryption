<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:24 PM
 */

namespace Giftcards\Encryption\Key;

class FallbackSource implements SourceInterface
{
    protected $fallbacks;
    protected $inner;
    protected $checking = array();

    /**
     * FallbackKeysSource constructor.
     * @param array $fallbacks
     * @param SourceInterface $inner
     */
    public function __construct(array $fallbacks, SourceInterface $inner)
    {
        $this->fallbacks = $fallbacks;
        $this->inner = $inner;
    }

    public function has($key)
    {
        foreach ($this->getKeys($key) as $key) {
            if ($this->inner->has($key)) {
                unset($this->checking[$key]);
                return true;
            }
        }
        unset($this->checking[$key]);

        return false;
    }

    public function get($key)
    {
        foreach ($this->getKeys($key) as $key) {
            if ($this->inner->has($key)) {
                unset($this->checking[$key]);
                return $this->inner->get($key);
            }
        }
        
        unset($this->checking[$key]);

        throw new KeyNotFoundException($key);
    }

    /**
     * @param $key
     * @return array
     */
    protected function getKeys($key)
    {
        $keys = isset($this->fallbacks[$key]) ? $this->fallbacks[$key] : array();
        if (!isset($this->checking[$key])) {
            array_unshift($keys, $key);
            $this->checking[$key] = true;
        }

        return $keys;
    }
}
