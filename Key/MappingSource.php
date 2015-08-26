<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:21 PM
 */

namespace Omni\Encryption\Key;

class MappingSource extends AbstractSource
{
    protected $map;
    protected $inner;

    /**
     * MapKeySource constructor.
     * @param array $map
     * @param SourceInterface $inner
     */
    public function __construct(array $map, SourceInterface $inner)
    {
        $this->map = $map;
        $this->inner = $inner;
    }

    public function has($key)
    {
        return $this->inner->has($this->mapKey($key));
    }

    protected function getKey($key)
    {
        return $this->inner->get($this->mapKey($key));
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function mapKey($key)
    {
        if (isset($this->map[$key])) {
            $key = $this->map[$key];
            return $key;
        }
        return $key;
    }
}
