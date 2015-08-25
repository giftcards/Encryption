<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:31 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

use InvalidArgumentException;

class RotatorNotFoundException extends \InvalidArgumentException
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(sprintf('The store named "%s" could not be found.', $name));
    }
}
