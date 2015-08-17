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
    protected $algorithmRegistry;
    protected $keySource;
    protected $profileRegistry;

    /**
     * CipherTextGenerator constructor.
     * @param CipherRegistry $algorithmRegistry
     * @param SourceInterface $keySource
     * @param ProfileRegistry $profileRegistry
     */
    public function __construct(
        CipherRegistry $algorithmRegistry,
        SourceInterface $keySource,
        ProfileRegistry $profileRegistry
    ) {
        $this->algorithmRegistry = $algorithmRegistry;
        $this->keySource = $keySource;
        $this->profileRegistry = $profileRegistry;
    }

    public function encrypt($clearText, $profile)
    {
        $profile = $this->profileRegistry->get($profile);
        $key = $this->keySource->get($profile->getKeyName());
        $algorithm = $this->algorithmRegistry->get($profile->getCipher());
        return new CipherText(
            $algorithm->encipher((string)$clearText, (string)$key),
            $profile
        );
    }

    public function decrypt(CipherTextInterface $cipherText)
    {
        $key = $this->keySource->get($cipherText->getProfile()->getKeyName());
        $algorithm = $this->algorithmRegistry->get($cipherText->getProfile()->getCipher());
        return $this->algorithmRegistry
            ->get($algorithm)
            ->decipher((string)$cipherText->getText(), (string)$key)
        ;
    }
}
