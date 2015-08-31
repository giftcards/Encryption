<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 5:26 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherTextInterface;

class ChainDeserializer implements DeserializerInterface
{
    /** @var DeserializerInterface[][] */
    protected $deserializers = array();
    /** @var DeserializerInterface[] */
    protected $sorted = array();

    public function add(DeserializerInterface $deserializer, $priority = 0)
    {
        $this->sorted = false;
        if (!isset($this->deserializers[$priority])) {
            $this->deserializers[$priority] = array();
        }
        
        $this->deserializers[$priority][] = $deserializer;
        return $this;
    }

    /**
     * @param $string
     * @return CipherTextInterface
     */
    public function deserialize($string)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return $serializer->deserialize($string);
            }
        }

        throw new FailedToDeserializeException($string);
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        $this->sort();
        foreach ($this->sorted as $serializer) {
            if ($serializer->canDeserialize($string)) {
                return true;
            }
        }

        return false;
    }

    protected function sort()
    {
        if ($this->sorted || !$this->deserializers) {
            return;
        }
        
        krsort($this->deserializers);
        $this->sorted = call_user_func_array('array_merge', $this->deserializers);
    }
}
