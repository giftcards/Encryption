<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 7:06 PM
 */

namespace Omni\Encryption\Key;

class ChainSource implements SourceInterface
{
    /** @var SourceInterface[] */
    protected $sources = array();
    
    public function add(SourceInterface $source)
    {
        $this->sources[] = $source;
        return $this;
    }
    
    public function has($key)
    {
        foreach ($this->sources as $source) {
            if ($source->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function get($key)
    {
        foreach ($this->sources as $source) {
            if ($source->has($source)) {
                return $source->get($key);
            }
        }
        
        throw new KeyNotFoundException($key);
    }
}
