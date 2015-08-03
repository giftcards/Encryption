<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 7:02 PM
 */

namespace Omni\Encryption\Key;

class StaticSource extends AbstractSource
{
    protected $name;
    protected $key;

    /**
     * StaticSource constructor.
     * @param $name
     * @param $key
     */
    public function __construct($name, $key)
    {
        $this->name = $name;
        $this->key = $key;
    }

    public function has($key)
    {
        return $this->name == $key;
    }

    protected function getKey($key)
    {
        return $this->key;
    }
}
