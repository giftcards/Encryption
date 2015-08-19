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
use Omni\Encryption\Key\SourceInterface;
use Omni\Encryption\Profile\ProfileRegistry;

class Encryptor
{
    protected $cipherRegistry;
    protected $keySource;
    protected $profileRegistry;
    protected $defaultProfile;

    /**
     * CipherTextGenerator constructor.
     * @param CipherRegistry $algorithmRegistry
     * @param SourceInterface $keySource
     * @param ProfileRegistry $profileRegistry
     * @param null $defaultProfile
     */
    public function __construct(
        CipherRegistry $algorithmRegistry,
        SourceInterface $keySource,
        ProfileRegistry $profileRegistry,
        $defaultProfile = null
    ) {
        $this->cipherRegistry = $algorithmRegistry;
        $this->keySource = $keySource;
        $this->profileRegistry = $profileRegistry;
        $this->defaultProfile = $defaultProfile;
    }

    public function encrypt($clearText, $profile = null)
    {
        $profile = $profile ?: $this->defaultProfile;
        $profile = $this->profileRegistry->get($profile);
        $key = $this->keySource->get($profile->getKeyName());
        $cipher = $this->cipherRegistry->get($profile->getCipher());
        return new CipherText(
            $cipher->encipher((string)$clearText, (string)$key),
            $profile
        );
    }

    public function decrypt(CipherTextInterface $cipherText)
    {
        $key = $this->keySource->get($cipherText->getProfile()->getKeyName());
        $cipher = $this->cipherRegistry->get($cipherText->getProfile()->getCipher());
        return $cipher->decipher((string)$cipherText->getText(), (string)$key);
    }
}
