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
        return isset($this->fallbacks[$key]) ? $this->fallbacks[$key] : array();
    }
}
