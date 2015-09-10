<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 5:50 PM
 */

namespace Giftcards\Encryption\Factory;

class WrongTypeBuiltException extends \RuntimeException
{
    protected $expectedType;
    protected $object;

    /**
     * FactoryNotFoundException constructor.
     * @param string $expectedType
     * @param int $returnedValue
     */
    public function __construct($expectedType, $returnedValue)
    {
        $this->expectedType = $expectedType;
        $this->object = $returnedValue;
        parent::__construct(sprintf(
            'The builder should have returned an instance of "%s". returned type is "%s".',
            $this->expectedType,
            is_object($returnedValue) ? get_class($returnedValue) : gettype($returnedValue)
        ));
    }

    /**
     * @return mixed
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }

    /**
     * @return int
     */
    public function getObject()
    {
        return $this->object;
    }
}
