<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 7:02 PM
 */

namespace Omni\Encryption\Key;

class ArraySource extends AbstractSource
{
    protected $keys;

    /**
     * StaticSource constructor.
     * @param array $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function has($key)
    {
        return isset($this->keys[$key]);
    }

    protected function getKey($key)
    {
        return $this->keys[$key];
    }
}
