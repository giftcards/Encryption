<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 4:52 PM
 */

namespace Omni\Encryption\Tests;

use Mockery\MockInterface;
use Omni\Encryption\Cipher\CipherRegistry;
use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\StringableCipherText;
use Omni\Encryption\Encryptor;
use Omni\Encryption\Profile\Profile;
use Omni\Encryption\Profile\ProfileRegistry;
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
    protected $serializer;
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
        $this->keySource = \Mockery::mock('Omni\Encryption\Key\SourceInterface');
        $this->profileRegistry = new ProfileRegistry();
        $this->serializer = \Mockery::mock('Omni\Encryption\CipherText\Serializer\SerializerInterface');
        $this->key1Name = $this->getFaker()->unique()->word;
        $this->key2Name = $this->getFaker()->unique()->word;
        $this->cipher1Name = $this->getFaker()->unique()->word;
        $this->cipher2Name = $this->getFaker()->unique()->word;
        $this->profile1Name = $this->getFaker()->unique()->word;
        $this->profile2Name = $this->getFaker()->unique()->word;
        $this->cipherRegistry
            ->add(
                \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
                    ->shouldReceive('getName')
                    ->andReturn($this->cipher1Name)
                    ->getMock()
            )
            ->add(
                \Mockery::mock('Omni\Encryption\Cipher\CipherInterface')
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
            $this->serializer,
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
                $this->serializer
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
                $this->serializer
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
            $this->serializer
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
        $this->serializer
            ->shouldReceive('deserialize')
            ->once()
            ->with($cipherText1)
            ->andReturn(new CipherText($cipherText1, $this->profileRegistry->get($this->profile1Name)))
        ;
        $this->assertEquals(
            $plainText1,
            $this->encryptor->decrypt($cipherText1)
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
