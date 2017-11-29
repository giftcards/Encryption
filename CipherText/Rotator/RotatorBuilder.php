<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/14/17
 * Time: 3:54 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;
use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistryBuilder;
use Giftcards\Encryption\Encryptor;
use Giftcards\Encryption\EncryptorBuilder;
use Giftcards\Encryption\Factory\BuilderInterface;

class RotatorBuilder
{

    // <editor-fold desc="Construction and Building">
    /**
     * @return RotatorBuilder
     */
    public static function newInstance()
    {
        return new self();
    }

    /**
     * RotatorBuilder constructor.
     * @param BuilderInterface[] $storeBuilders
     */
    public function __construct($storeBuilders = array())
    {
        $this->storeRegistryBuilder = StoreRegistryBuilder::newInstance($storeBuilders);
    }

    /**
     * @return Rotator
     */
    public function build()
    {
        return new Rotator($this->buildEncryptor(), $this->buildStoreRegistry());
    }

    // </editor-fold>

    // <editor-fold desc="Store Registry">
    /**
     * @var StoreRegistryBuilder
     */
    private $storeRegistryBuilder;

    /**
     * Adds a store to the builder
     * @param $storeName
     * @param $builderName
     * @param array $options
     */
    public function addStore($storeName, $builderName, array $options = array())
    {
        $this->storeRegistryBuilder->addStore($storeName, $builderName, $options);
    }

    private function buildStoreRegistry()
    {
        return $this->storeRegistryBuilder->build();
    }
    // </editor-fold>

    // <editor-fold desc="Encryptor Stuff">
    /**
     * @var Encryptor|EncryptorBuilder
     */
    private $encryptor;

    /**
     * @param Encryptor|EncryptorBuilder $encryptor
     * @throws \TypeError
     */
    public function setEncryptor($encryptor)
    {
        if (!($encryptor instanceof Encryptor || $encryptor instanceof EncryptorBuilder)) {
            throw new \TypeError(sprintf(
                "Argument for RotatorBuilder::setEncryptor must be either Encryptor or EncryptorBuilder. %s given",
                is_object($encryptor) ? get_class($encryptor) : gettype($encryptor)
            ));
        }
        $this->encryptor = $encryptor;
    }

    /**
     * @return Encryptor|EncryptorBuilder
     */
    public function getEncryptor()
    {
        if (!isset($this->encryptor)) {
            $this->setEncryptor(EncryptorBuilder::newInstance());
        }
        return $this->encryptor;
    }

    /**
     * @return Encryptor
     */
    private function buildEncryptor()
    {
        if ($this->getEncryptor() instanceof EncryptorBuilder) {
            return $this->getEncryptor()->build();
        }
        return $this->getEncryptor();
    }
    // </editor-fold>
}
