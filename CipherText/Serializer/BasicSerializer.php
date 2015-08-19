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

class BasicSerializer implements SerializerInterface
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
     * @return string
     */
    public function serialize(CipherTextInterface $cipherText)
    {
        $profile = array(
            'key_name' => $cipherText->getProfile()->getKeyName(),
            'cipher' => $cipherText->getProfile()->getCipher()
        );
        return base64_encode(json_encode($profile)) . ':' . base64_encode($cipherText->getText());
    }

    /**
     * @param string
     * @return CipherTextInterface
     */
    public function unserialize($string)
    {
        if (stripos($string, $this->separator) === false) {
            throw new FailedToUnserializeException(
                $string,
                sprintf('The separator %s is not in the given string.', $this->separator)
            );
        }

        list($profile, $text) = explode($this->separator, $string, 2);
        
        $profile = json_decode(base64_decode($profile));
        $text = base64_decode($text);
        
        if (is_null($profile)) {
            throw new FailedToUnserializeProfileException($string);
        }
        
        if ($text === false) {
            throw new FailedToUnserializeTextException($string);
        }
        
        return new CipherText($text, $profile);
    }
}
