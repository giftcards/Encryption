<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 8:54 PM
 */

namespace Omni\Encryption;

use Omni\Encryption\Cipher\CipherRegistry;
use Omni\Encryption\Cipher\CipherRegistryBuilder;
use Omni\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Omni\Encryption\CipherText\Serializer\ChainSerializer;
use Omni\Encryption\CipherText\Serializer\DeserializerInterface;
use Omni\Encryption\CipherText\Serializer\SerializerDeserializerBuilder;
use Omni\Encryption\CipherText\Serializer\SerializerInterface;
use Omni\Encryption\Key\ChainSource;
use Omni\Encryption\Key\SourceBuilder;
use Omni\Encryption\Key\SourceInterface;
use Omni\Encryption\Profile\ProfileRegistry;

class EncryptorBuilder
{
    protected $cipherRegistryBuilder;
    protected $keySourceBuilder;
    protected $profileRegistry;
    protected $serializerDeserializerBuilder;
    protected $defaultProfile;
    
    public static function create()
    {
        return new static();
    }

    public function build()
    {
        return new Encryptor(
            $this->getCipherRegistryBuilder()->build(),
            $this->getKeySourceBuilder()->build(),
            $this->getProfileRegistry(),
            $this->getSerializerDeserializerBuilder()->build(),
            $this->getDefaultProfile()
        );
    }

    /**
     * @return CipherRegistryBuilder
     */
    public function getCipherRegistryBuilder()
    {
        if (!$this->cipherRegistryBuilder) {
            $this->cipherRegistryBuilder = $this->getDefaultCipherRegistryBuilder();
        }
        
        return $this->cipherRegistryBuilder;
    }

    /**
     * @param CipherRegistryBuilder $cipherRegistryBuilder
     * @return $this
     */
    public function setCipherRegistryBuilder(CipherRegistryBuilder $cipherRegistryBuilder)
    {
        $this->cipherRegistryBuilder = $cipherRegistryBuilder;
        return $this;
    }

    /**
     * @return SourceBuilder
     */
    public function getKeySourceBuilder()
    {
        if (!$this->keySourceBuilder) {
            $this->keySourceBuilder = $this->getDefaultKeySourceBuilder();
        }

        return $this->keySourceBuilder;
    }

    /**
     * @param SourceBuilder $keySourceBuilder
     * @return $this
     */
    public function setKeySourceBuilder(SourceBuilder $keySourceBuilder)
    {
        $this->keySourceBuilder = $keySourceBuilder;
        return $this;
    }

    /**
     * @return ProfileRegistry
     */
    public function getProfileRegistry()
    {
        if (!$this->profileRegistry) {
            $this->profileRegistry = $this->getDefaultProfileRegistry();
        }

        return $this->profileRegistry;
    }

    /**
     * @param ProfileRegistry $profileRegistry
     * @return $this
     */
    public function setProfileRegistry(ProfileRegistry $profileRegistry)
    {
        $this->profileRegistry = $profileRegistry;
        return $this;
    }

    /**
     * @return SerializerDeserializerBuilder
     */
    public function getSerializerDeserializerBuilder()
    {
        if (!$this->serializerDeserializerBuilder) {
            $this->serializerDeserializerBuilder = $this->getDefaultSerializerDeserializerBuilder();
        }

        return $this->serializerDeserializerBuilder;
    }

    /**
     * @param SerializerDeserializerBuilder $serializerDeserializerBuilder
     * @return $this
     */
    public function setSerializerDeserializerBuilder(SerializerDeserializerBuilder $serializerDeserializerBuilder)
    {
        $this->serializerDeserializerBuilder = $serializerDeserializerBuilder;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultProfile()
    {
        return $this->defaultProfile;
    }

    /**
     * @param string $defaultProfile
     * @return $this
     */
    public function setDefaultProfile($defaultProfile)
    {
        $this->defaultProfile = $defaultProfile;
        return $this;
    }

    protected function getDefaultCipherRegistryBuilder()
    {
        return CipherRegistryBuilder::newInstance();
    }

    protected function getDefaultKeySourceBuilder()
    {
        return SourceBuilder::newInstance();
    }

    /**
     * @return ProfileRegistry
     */
    protected function getDefaultProfileRegistry()
    {
        return new ProfileRegistry();
    }

    /**
     * @return SerializerDeserializerBuilder
     */
    protected function getDefaultSerializerDeserializerBuilder()
    {
        return SerializerDeserializerBuilder::newInstance();
    }
}
