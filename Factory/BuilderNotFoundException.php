<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 5:50 PM
 */

namespace Omni\Encryption\Factory;

use InvalidArgumentException;

class BuilderNotFoundException extends \InvalidArgumentException
{
    protected $name;

    /**
     * FactoryNotFoundException constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(sprintf('The builder named "%s" was not found.', $name));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
