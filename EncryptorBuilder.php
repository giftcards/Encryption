<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 8:54 PM
 */

namespace Omni\Encryption;

use Omni\Encryption\Cipher\CipherRegistry;
use Omni\Encryption\CipherText\Serializer\ChainDeserializer;
use Omni\Encryption\CipherText\Serializer\ChainSerializer;
use Omni\Encryption\CipherText\Serializer\DeserializerInterface;
use Omni\Encryption\CipherText\Serializer\SerializerInterface;
use Omni\Encryption\Key\ChainSource;
use Omni\Encryption\Key\SourceInterface;
use Omni\Encryption\Profile\ProfileRegistry;

class EncryptorBuilder
{
    protected $cipherRegistry;
    protected $keySource;
    protected $profileRegistry;
    protected $serializer;
    protected $deserializer;
    protected $defaultProfile;
    
    public static function create()
    {
        return new static();
    }

    public function build()
    {
        return new Encryptor(
            $this->getCipherRegistry(),
            $this->getKeySource(),
            $this->getProfileRegistry(),
            $this->getSerializer(),
            $this->getDeserializer(),
            $this->getDefaultProfile()
        );
    }

    /**
     * @return CipherRegistry
     */
    public function getCipherRegistry()
    {
        if (!$this->cipherRegistry) {
            $this->cipherRegistry = $this->getDefaultCipherRegistry();
        }
        
        return $this->cipherRegistry;
    }

    /**
     * @param CipherRegistry $cipherRegistry
     * @return $this
     */
    public function setCipherRegistry(CipherRegistry $cipherRegistry)
    {
        $this->cipherRegistry = $cipherRegistry;
        return $this;
    }

    /**
     * @return SourceInterface
     */
    public function getKeySource()
    {
        if (!$this->keySource) {
            $this->keySource = $this->getDefaultKeySource();
        }

        return $this->keySource;
    }

    /**
     * @param SourceInterface $keySource
     * @return $this
     */
    public function setKeySource(SourceInterface $keySource)
    {
        $this->keySource = $keySource;
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
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        if (!$this->serializer) {
            $this->serializer = $this->getDefaultSerializer();
        }

        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @return DeserializerInterface
     */
    public function getDeserializer()
    {
        if (!$this->deserializer) {
            $this->deserializer = $this->getDefaultDeserializer();
        }

        return $this->deserializer;
    }

    /**
     * @param DeserializerInterface $deserializer
     * @return $this
     */
    public function setDeserializer(DeserializerInterface $deserializer)
    {
        $this->deserializer = $deserializer;
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

    protected function getDefaultCipherRegistry()
    {
        return new CipherRegistry();
    }

    protected function getDefaultKeySource()
    {
        return new ChainSource();
    }

    /**
     * @return ProfileRegistry
     */
    protected function getDefaultProfileRegistry()
    {
        return new ProfileRegistry();
    }

    /**
     * @return ChainSerializer
     */
    protected function getDefaultSerializer()
    {
        return new ChainSerializer();
    }

    /**
     * @return ChainDeserializer
     */
    protected function getDefaultDeserializer()
    {
        return new ChainDeserializer();
    }
}
