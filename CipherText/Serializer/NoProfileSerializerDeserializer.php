<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:29 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer;

use Giftcards\Encryption\CipherText\CipherText;
use Giftcards\Encryption\CipherText\CipherTextInterface;
use Giftcards\Encryption\Profile\Profile;

class NoProfileSerializerDeserializer extends AbstractSerializerDeserializer
{
    protected $profile;
    protected $inner;

    /**
     * FallbackProfileSerializer constructor.
     * @param $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @param CipherTextInterface $cipherText
     * @return bool
     */
    public function canSerialize(CipherTextInterface $cipherText)
    {
        return $this->profile->equals($cipherText->getProfile());
    }

    /**
     * @param $string
     * @return bool
     */
    public function canDeserialize($string)
    {
        return true;
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
}
