<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 6:00 PM
 */

namespace Omni\Encryption\Tests;

use Omni\Encryption\Cipher\CipherRegistry;
use Omni\Encryption\Cipher\MysqlAes;
use Omni\Encryption\Cipher\NoOp;
use Omni\Encryption\CipherText\Serializer\ChainSerializerDeserializer;
use Omni\Encryption\Encryptor;
use Omni\Encryption\EncryptorBuilder;
use Omni\Encryption\Key\ChainSource;
use Omni\Encryption\Profile\ProfileRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class EncryptorBuilderTest extends AbstractExtendableTestCase
{
    /** @var  EncryptorBuilder */
    protected $encryptorBuilder;

    public function setUp()
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
