<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/17/15
 * Time: 12:25 PM
 */

namespace Giftcards\Encryption\Key;

class CircularGuardSource implements SourceInterface
{
    protected $checking = array();
    protected $inner;

    public function __construct(SourceInterface $inner)
    {
        $this->inner = $inner;
    }

    public function has($key)
    {
        if (!empty($this->checking[$key])) {
            return false;
        }
        
        $this->checking[$key] = true;
        
        try {
            $has = $this->inner->has($key);
        } catch (\Exception $e) {
            unset($this->checking[$key]);
            throw $e;
        }
        
        return $has;
    }

    public function get($key)
    {
        if (!empty($this->checking[$key])) {
            throw new KeyNotFoundException($key);
        }
        
        $this->checking[$key] = true;

        try {
            $value = $this->inner->get($key);
        } catch (\Exception $e) {
            unset($this->checking[$key]);
            throw $e;
        }

        return $value;
    }
}
