<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 5:42 PM
 */

namespace Giftcards\Encryption\Key;

class IniFileSource extends AbstractSource
{
    protected $keys;
    protected $file;
    protected $caseSensitive;

    /**
     * IniFileSource constructor.
     * @param $file
     * @param bool $caseSensitive
     */
    public function __construct($file, $caseSensitive = false)
    {
        $this->file = $file;
        $this->caseSensitive = $caseSensitive;
    }

    public function has($key)
    {
        $this->loadFile();
        return isset($this->keys[$this->normalizeKey($key)]);
    }

    protected function getKey($key)
    {
        return $this->keys[$this->normalizeKey($key)];
    }

    protected function loadFile()
    {
        if (is_array($this->keys)) {
            return;
        }
        
        $this->keys = parse_ini_file($this->file);
        
        if (!$this->caseSensitive) {
            $this->keys = array_combine(
                array_map('strtolower', array_keys($this->keys)),
                $this->keys
            );
        }
    }

    protected function normalizeKey($key)
    {
        if (!$this->caseSensitive) {
            $key = strtolower($key);
        }
        
        return $key;
    }
}
