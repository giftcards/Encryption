<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/14/17
 * Time: 3:54 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistryBuilder;
use Giftcards\Encryption\EncryptorBuilder;
use Giftcards\Encryption\Factory\BuilderInterface;

class RotatorBuilder
{

    /**
     * @var EncryptorBuilder
     */
    private $encryptorBuilder;

    /**
     * @var StoreRegistryBuilder
     */
    private $storeRegistryBuilder;

    /**
     * RotatorBuilder constructor.
     * @param BuilderInterface[] $builders
     */
    public function __construct($builders = [])
    {
        $this->encryptorBuilder = new EncryptorBuilder();
        $this->storeRegistryBuilder = new StoreRegistryBuilder($builders);
    }

    /**
     * @return EncryptorBuilder
     */
    public function getEncryptorBuilder(): EncryptorBuilder
    {
        return $this->encryptorBuilder;
    }

    /**
     * @return StoreRegistryBuilder
     */
    public function getStoreRegistryBuilder(): StoreRegistryBuilder
    {
        return $this->storeRegistryBuilder;
    }

    /**
     * @return Rotator
     */
    public function build(): Rotator
    {
        return new Rotator($this->encryptorBuilder->build(),$this->storeRegistryBuilder->build());
    }

}