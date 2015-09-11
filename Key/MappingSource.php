<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:21 PM
 */

namespace Giftcards\Encryption\Key;

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
        return isset($this->map[$key]) && $this->inner->has($this->map[$key]);
    }

    protected function getKey($key)
    {
        return $this->inner->get($this->map[$key]);
    }
}
