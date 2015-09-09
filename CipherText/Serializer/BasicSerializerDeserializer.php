<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/19/15
 * Time: 1:19 PM
 */

namespace Omni\Encryption\CipherText\Serializer;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\CipherTextInterface;
use Omni\Encryption\Profile\Profile;

class BasicSerializerDeserializer extends AbstractSerializerDeserializer
{
    protected $separator;

    /**
     * BasicSerializer constructor.
     * @param $separator
     */
    public function __construct($separator = ':')
    {
        $this->separator = $separator;
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
        if (stripos($string, $this->separator) === false) {
            return false;
        }

        list($profile, $text) = explode($this->separator, $string, 2);

        $profile = json_decode(base64_decode($profile));
        $text = base64_decode($text);

        return !is_null($profile) && $text !== false;
    }

    /**
     * @param $cipherText
     * @return string
     */
    protected function doSerialize(CipherTextInterface $cipherText)
    {
        $profile = array(
            'key_name' => $cipherText->getProfile()->getKeyName(),
            'cipher' => $cipherText->getProfile()->getCipher()
        );
        return sprintf(
            '%s%s%s',
            base64_encode(json_encode($profile)),
            $this->separator,
            base64_encode($cipherText->getText())
        );
    }

    /**
     * @param $string
     * @return CipherTextInterface
     */
    protected function doDeserialize($string)
    {
        list($profile, $text) = explode($this->separator, $string, 2);
        $profile = json_decode(base64_decode($profile), true);
        return new CipherText(base64_decode($text), new Profile($profile['cipher'], $profile['key_name']));

    }
}
