<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 8:54 PM
 */

namespace Giftcards\Encryption;

use Giftcards\Encryption\Cipher\CipherInterface;
use Giftcards\Encryption\Cipher\CipherRegistryBuilder;
use Giftcards\Encryption\CipherText\Serializer\SerializerDeserializerBuilder;
use Giftcards\Encryption\Key\SourceBuilder;
use Giftcards\Encryption\Profile\ProfileRegistry;
use Giftcards\Encryption\Profile\ProfileRegistryBuilder;

class EncryptorBuilder
{
    protected $cipherRegistryBuilder;
    protected $keySourceBuilder;
    protected $profileRegistryBuilder;
    protected $serializerDeserializerBuilder;
    protected $defaultProfile;
    
    public static function newInstance()
    {
        return new static();
    }

    public function build()
    {
        return new Encryptor(
            $this->getCipherRegistryBuilder()->build(),
            $this->getKeySourceBuilder()->build(),
            $this->getProfileRegistryBuilder()->build(),
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
     * @return ProfileRegistryBuilder
     */
    public function getProfileRegistryBuilder()
    {
        if (!$this->profileRegistryBuilder) {
            $this->profileRegistryBuilder = $this->getDefaultProfileRegistryBuilder();
        }

        return $this->profileRegistryBuilder;
    }

    /**
     * @param ProfileRegistryBuilder $profileRegistryBuilder
     * @return $this
     */
    public function setProfileRegistryBuilder(ProfileRegistryBuilder $profileRegistryBuilder)
    {
        $this->profileRegistryBuilder = $profileRegistryBuilder;
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

    public function addCipher(CipherInterface $cipher)
    {
        $this->getCipherRegistryBuilder()->add($cipher);
        return $this;
    }

    public function addKeySource(
        $source,
        array $options = array(),
        $prefix = null,
        $addCircularGuard = false
    ) {
        $this->getKeySourceBuilder()->add($source, $options, $prefix, $addCircularGuard);
        return $this;
    }

    public function setProfile($name, $profileOrCipher, $key = null)
    {
        $this->getProfileRegistryBuilder()->set($name, $profileOrCipher, $key);
        return $this;
    }

    public function addSerializer($serializer, array $options = array())
    {
        $this->getSerializerDeserializerBuilder()->addSerializer($serializer, $options);
        return $this;
    }

    public function addDeserializer($deserializer, array $options = array())
    {
        $this->getSerializerDeserializerBuilder()->addDeserializer($deserializer, $options);
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
     * @return ProfileRegistryBuilder
     */
    protected function getDefaultProfileRegistryBuilder()
    {
        return ProfileRegistryBuilder::newInstance();
    }

    /**
     * @return SerializerDeserializerBuilder
     */
    protected function getDefaultSerializerDeserializerBuilder()
    {
        return SerializerDeserializerBuilder::newInstance();
    }
}
