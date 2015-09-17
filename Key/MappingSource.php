<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:21 PM
 */

namespace Giftcards\Encryption\Key;

class MappingSource implements SourceInterface
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

    public function get($key)
    {
        return $this->inner->get($this->mapKey($key));
    }
    
    /**
     * @param $key
     * @return mixed
     */
    protected function mapKey($key)
    {
        return isset($this->map[$key]) ? $this->map[$key] : $key;
    }
}
