<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:29 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\CipherTextInterface;

class FallbackProfileSerializer implements SerializerInterface
{
    protected $fallbackProfile;
    protected $inner;

    /**
     * FallbackProfileSerializer constructor.
     * @param $fallbackProfile
     * @param $inner
     */
    public function __construct($fallbackProfile, SerializerInterface $inner)
    {
        $this->fallbackProfile = $fallbackProfile;
        $this->inner = $inner;
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        return $this->inner->serialize($cipherText);
    }

    /**
     * @param string $string
     * @return CipherTextInterface
     */
    public function unserialize($string)
    {
        try {
            return $this->inner->unserialize($string);
        } catch (FailedToUnserializeException $e) {
            return new CipherText($string, $this->fallbackProfile);
        }
    }
}
