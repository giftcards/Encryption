<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:03 PM
 */

namespace Omni\Encryption\Key;

class KeyNotFoundException extends \InvalidArgumentException
{
    protected $keyName;

    public function __construct($keyName)
    {
        $this->keyName = $keyName;
        parent::__construct(sprintf('The key named "%s" could not be found.', $keyName));
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }
}