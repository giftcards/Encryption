<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 4:52 PM
 */

namespace Giftcards\Encryption\Tests;

use Mockery\MockInterface;
use Giftcards\Encryption\Cipher\CipherRegistry;
use Giftcards\Encryption\CipherText\CipherText;
use Giftcards\Encryption\CipherText\StringableCipherText;
use Giftcards\Encryption\Encryptor;
use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Profile\ProfileRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class EncryptorTest extends AbstractExtendableTestCase
{
    /** @var  Encryptor */
    protected $encryptor;
    /** @var  CipherRegistry */
    protected $cipherRegistry;
    /** @var  MockInterface */
    protected $keySource;
    /** @var  ProfileRegistry */
    protected $profileRegistry;
    /** @var  MockInterface */
    protected $serializerDeserializer;
    protected $defaultProfile;
    protected $key1Name;
    protected $key2Name;
    protected $cipher1Name;
    protected $cipher2Name;
    protected $profile1Name;
    protected $profile2Name;

    public function setUp()
    {
        $this->cipherRegistry = new CipherRegistry();
        $this->keySource = \Mockery::mock('Giftcards\Encryption\Key\SourceInterface');
        $this->profileRegistry = new ProfileRegistry();
        $this->serializerDeserializer = \Mockery::mock(
            'Giftcards\Encryption\CipherText\Serializer\SerializerDeserializerInterface'
        );
        $this->key1Name = $this->getFaker()->unique()->word;
        $this->key2Name = $this->getFaker()->unique()->word;
        $this->cipher1Name = $this->getFaker()->unique()->word;
        $this->cipher2Name = $this->getFaker()->unique()->word;
        $this->profile1Name = $this->getFaker()->unique()->word;
        $this->profile2Name = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->add(
                \Mockery::mock('Giftcards\Encryption\Cipher\CipherInterface')
                    ->shouldReceive('getName')
                    ->andReturn($this->cipher1Name)
                    ->getMock()
            )
            ->add(
                \Mockery::mock('Giftcards\Encryption\Cipher\CipherInterface')
                    ->shouldReceive('getName')
                    ->andReturn($this->cipher2Name)
                    ->getMock()
            )
        ;
        $this->keySource
            ->shouldReceive('get')
            ->with($this->key1Name)
            ->andReturn($this->getFaker()->unique()->word)
            ->getMock()
            ->shouldReceive('get')
            ->with($this->key2Name)
            ->andReturn($this->getFaker()->unique()->word)
            ->getMock()
        ;
        $this->profileRegistry
            ->set($this->profile1Name, new Profile($this->cipher1Name, $this->key1Name))
            ->set($this->profile2Name, new Profile($this->cipher2Name, $this->key2Name))
        ;
        $this->encryptor = new Encryptor(
            $this->cipherRegistry,
            $this->keySource,
            $this->profileRegistry,
            $this->serializerDeserializer,
            $this->defaultProfile = $this->profile2Name
        );
    }

    public function testEncrypt()
    {
        $cipherText1 = $this->getFaker()->unique()->word;
        $plainText1 = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->get($this->cipher1Name)
            ->shouldReceive('encipher')
            ->once()
            ->with($plainText1, $this->keySource->get($this->key1Name))
            ->andReturn($cipherText1)
        ;
        $this->assertEquals(
            new StringableCipherText(
                new CipherText($cipherText1, $this->profileRegistry->get($this->profile1Name)),
                $this->serializerDeserializer
            ),
            $this->encryptor->encrypt($plainText1, $this->profile1Name)
        );
        $cipherText2 = $this->getFaker()->unique()->word;
        $plainText2 = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->get($this->cipher2Name)
            ->shouldReceive('encipher')
            ->once()
            ->with($plainText2, $this->keySource->get($this->key2Name))
            ->andReturn($cipherText2)
        ;
        $this->assertEquals(
            new StringableCipherText(
                new CipherText($cipherText2, $this->profileRegistry->get($this->profile2Name)),
                $this->serializerDeserializer
            ),
            $this->encryptor->encrypt($plainText2)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEncryptWhereProfileCantBeChosen()
    {
        $this->encryptor = new Encryptor(
            $this->cipherRegistry,
            $this->keySource,
            $this->profileRegistry,
            $this->serializerDeserializer
        );
        $plainText = $this->getFaker()->unique()->word;
        $this->encryptor->encrypt($plainText);
    }

    public function testDecrypt()
    {
        $cipherText1 = $this->getFaker()->unique()->word;
        $plainText1 = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->get($this->cipher1Name)
            ->shouldReceive('decipher')
            ->once()
            ->with($cipherText1, $this->keySource->get($this->key1Name))
            ->andReturn($plainText1)
        ;
        $this->serializerDeserializer
            ->shouldReceive('deserialize')
            ->once()
            ->with($cipherText1)
            ->andReturn(new CipherText($cipherText1, $this->profileRegistry->get($this->profile1Name)))
        ;
        $this->assertEquals(
            $plainText1,
            $this->encryptor->decrypt($cipherText1)
        );
        $this->cipherRegistry
            ->get($this->cipher1Name)
            ->shouldReceive('decipher')
            ->once()
            ->with($cipherText1, $this->keySource->get($this->key1Name))
            ->andReturn($plainText1)
        ;
        $this->serializerDeserializer
            ->shouldReceive('deserialize')
            ->once()
            ->with($cipherText1)
            ->andReturn(new CipherText($cipherText1, $this->profileRegistry->get($this->profile1Name)))
        ;
        $this->assertEquals(
            $plainText1,
            $this->encryptor->decrypt($cipherText1, $this->profile1Name)
        );
        $cipherText2 = $this->getFaker()->unique()->word;
        $plainText2 = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->get($this->cipher2Name)
            ->shouldReceive('decipher')
            ->once()
            ->with($cipherText2, $this->keySource->get($this->key2Name))
            ->andReturn($plainText2)
        ;
        $this->assertEquals(
            $plainText2,
            $this->encryptor->decrypt(new CipherText($cipherText2, $this->profileRegistry->get($this->profile2Name)))
        );
    }
}
