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
use Omni\Encryption\Profile\Profile;

class TextOnlySerializer extends AbstractSerializer
{
    protected $profile;
    protected $inner;

    /**
     * FallbackProfileSerializer constructor.
     * @param $profile
     * @param $inner
     */
    public function __construct(Profile $profile, SerializerInterface $inner)
    {
        $this->profile = $profile;
        $this->inner = $inner;
    }

    /**
     * @param $cipherText
     * @return string
     */
    protected function doSerialize(CipherTextInterface $cipherText)
    {
        return $cipherText->getText();
    }

    /**
     * @param $string
     * @return CipherTextInterface
     */
    protected function doDeserialize($string)
    {
        return new CipherText($string, $this->profile);
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText)
    {
        return true;
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        return true;
    }
}
