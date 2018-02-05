<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 2/5/18
 * Time: 6:14 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use InvalidArgumentException;

class StoreNotFoundException extends InvalidArgumentException
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(sprintf('The store named "%s" could not be found.', $name));
    }
}
