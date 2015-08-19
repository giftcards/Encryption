<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 7:09 PM
 */

namespace Omni\Encryption\CipherText;

use Omni\Encryption\CipherText\Serializer\SerializerInterface;
use Omni\Encryption\Profile\Profile;

class StringableCipherText implements CipherTextInterface
{
    protected $inner;
    protected $serializer;

    /**
     * StringableCipherText constructor.
     * @param $inner
     * @param $serializer
     */
    public function __construct(CipherTextInterface $inner, SerializerInterface $serializer)
    {
        $this->inner = $inner;
        $this->serializer = $serializer;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->inner->getText();
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->inner->getProfile();
    }

    public function __toString()
    {
        return $this->serializer->serialize($this->inner);
    }
}
