<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 9:33 PM
 */

namespace Omni\Encryption;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\Cipher\CipherRegistry;
use Omni\Encryption\CipherText\CipherTextInterface;
use Omni\Encryption\CipherText\Serializer\SerializerInterface;
use Omni\Encryption\CipherText\StringableCipherText;
use Omni\Encryption\Key\SourceInterface;
use Omni\Encryption\Profile\ProfileRegistry;

class Encryptor
{
    protected $cipherRegistry;
    protected $keySource;
    protected $profileRegistry;
    protected $defaultProfile;
    protected $serializer;

    /**
     * CipherTextGenerator constructor.
     * @param CipherRegistry $cipherRegistry
     * @param SourceInterface $keySource
     * @param ProfileRegistry $profileRegistry
     * @param SerializerInterface $serializer
     * @param string|null $defaultProfile
     */
    public function __construct(
        CipherRegistry $cipherRegistry,
        SourceInterface $keySource,
        ProfileRegistry $profileRegistry,
        SerializerInterface $serializer,
        $defaultProfile = null
    ) {
        $this->cipherRegistry = $cipherRegistry;
        $this->keySource = $keySource;
        $this->profileRegistry = $profileRegistry;
        $this->defaultProfile = $defaultProfile;
        $this->serializer = $serializer;
    }

    public function encrypt($clearText, $profile = null)
    {
        $profile = $profile ?: $this->defaultProfile;
        
        if (!$profile) {
            throw new \RuntimeException(
                '$profile must either be passed or the $defaultProfile must be defined in the constructor'
            );
        }
        
        $profile = $this->profileRegistry->get($profile);
        $key = $this->keySource->get($profile->getKeyName());
        $cipher = $this->cipherRegistry->get($profile->getCipher());
        
        return new StringableCipherText(new CipherText(
            $cipher->encipher((string)$clearText, (string)$key),
            $profile
        ), $this->serializer);
    }

    public function decrypt($cipherText, $profile = null)
    {
        if (!$cipherText instanceof CipherTextInterface) {
            if ($profile) {
                $cipherText = new CipherText($cipherText, $this->profileRegistry->get($profile));
            } else {
                $cipherText = $this->serializer->deserialize($cipherText);
            }
        }
        
        $key = $this->keySource->get($cipherText->getProfile()->getKeyName());
        $cipher = $this->cipherRegistry->get($cipherText->getProfile()->getCipher());
        
        return $cipher->decipher((string)$cipherText->getText(), (string)$key);
    }
}
