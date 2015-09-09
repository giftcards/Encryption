<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:31 PM
 */

namespace Giftcards\Encryption\Profile;

class ProfileNotFoundException extends \InvalidArgumentException
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(sprintf('The encryption profile named "%s" could not be found.', $name));
    }
}
