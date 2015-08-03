<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:46 PM
 */

namespace Omni\Encryption\Encryptor;

class EncryptorNotFoundException extends \InvalidArgumentException
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(sprintf('The encryptor named "%s" was not found.', $name));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
