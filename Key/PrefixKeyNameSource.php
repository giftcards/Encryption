<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 6:02 PM
 */

namespace Giftcards\Encryption\Key;

class PrefixKeyNameSource extends AbstractSource
{
    protected $prefix;
    protected $innerSource;
    protected $separator;

    /**
     * PrefixKeyNameSource constructor.
     * @param $prefix
     * @param SourceInterface $innerSource
     * @param string $separator
     */
    public function __construct($prefix, SourceInterface $innerSource, $separator = ':')
    {
        $this->prefix = $prefix;
        $this->innerSource = $innerSource;
        $this->separator = $separator;
    }

    public function has($key)
    {
        if (stripos($key, $this->prefix.$this->separator) !== 0) {
            return false;
        }
        
        return $this->innerSource->has(substr($key, strlen($this->prefix) + strlen($this->separator)));
    }

    protected function getKey($key)
    {
        return $this->innerSource->get(substr($key, strlen($this->prefix) + strlen($this->separator)));
    }
}
