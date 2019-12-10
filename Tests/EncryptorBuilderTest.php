<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 6:00 PM
 */

namespace Giftcards\Encryption\Tests;

use Giftcards\Encryption\Cipher\CipherRegistry;
use Giftcards\Encryption\Cipher\MysqlAes;
use Giftcards\Encryption\Cipher\NoOp;
use Giftcards\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Giftcards\Encryption\Encryptor;
use Giftcards\Encryption\EncryptorBuilder;
use Giftcards\Encryption\Key\ChainSource;
use Giftcards\Encryption\Profile\ProfileRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class EncryptorBuilderTest extends AbstractExtendableTestCase
{
    /** @var  EncryptorBuilder */
    protected $encryptorBuilder;

    public function setUp() : void
    {
        $this->encryptorBuilder = new EncryptorBuilder();
    }

    public function testBuild()
    {
        $cipherRegistry = new CipherRegistry();
        $cipherRegistry
            ->add(new MysqlAes())
            ->add(new NoOp())
        ;
        $keySource = new ChainSource();
        $profileRegistry = new ProfileRegistry();
        $serializerDeserializer = new ChainSerializerDeserializer();
        $this->assertEquals(new Encryptor(
            $cipherRegistry,
            $keySource,
            $profileRegistry,
            $serializerDeserializer,
            null
        ), $this->encryptorBuilder->build());
    }
}
