<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:44 PM
 */

namespace Giftcards\Encryption\Tests;

class MockPDO extends \PDO
{
    protected $innerMock;

    public function __construct($innerMock)
    {
        $this->innerMock = $innerMock;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->innerMock, $name), $args);
    }

    public function prepare($statement, $driver_options = null)
    {
        return $this->__call('prepare', func_get_args());
    }
}
