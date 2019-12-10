<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 12/8/16
 * Time: 3:50 PM
 */

namespace Giftcards\Encryption\Tests\Doctrine;

use Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver\AnnotationDriver;
use Giftcards\Encryption\Doctrine\FieldEncryptor;

use Mockery;
use Mockery\MockInterface;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use ReflectionProperty;

class FieldEncryptorTest extends AbstractExtendableTestCase
{
    /** @var  FieldEncryptor */
    protected $fieldEncryptor;
    /** @var  MockInterface */
    protected $encryptor;
    /** @var  AnnotationDriver */
    protected $driver;

    public function setUp() : void
    {
        $this->fieldEncryptor = new FieldEncryptor(
            $this->encryptor = Mockery::mock('Giftcards\Encryption\Encryptor')
        );
    }

    public function testEncryption()
    {
        $reflectionProperty1 = new ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties',
            'encryptedProperty'
        );

        $entity1EncryptedProperty = $this->getFaker()->unique()->word;
        $entity1EncryptedPropertyEncrypted = $this->getFaker()->unique()->word;
        $entity2EncryptedProperty = $this->getFaker()->unique()->word;
        $entity2EncryptedPropertyEncrypted = $this->getFaker()->unique()->word;

        $entity1 = new MockEntityWithEncryptedProperties();
        $entity1->encryptedProperty = $entity1EncryptedProperty;
        $entity2 = new MockEntityWithEncryptedProperties();
        $entity2->encryptedProperty = $entity2EncryptedProperty;

        $this->encryptor
            ->shouldReceive('encrypt')
            ->with($entity1EncryptedProperty, null)
            ->once()
            ->andReturn($entity1EncryptedPropertyEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity1EncryptedPropertyEncrypted, null)
            ->once()
            ->andReturn($entity1EncryptedProperty)
            ->getMock()
            ->shouldReceive('encrypt')
            ->with($entity2EncryptedProperty, 'foo')
            ->once()
            ->andReturn($entity2EncryptedPropertyEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity2EncryptedPropertyEncrypted, 'foo')
            ->once()
            ->andReturn($entity2EncryptedProperty)
            ->getMock()
            ->shouldReceive('encrypt')
            ->with($entity2EncryptedProperty, 'bar')
            ->once()
            ->andReturn($entity2EncryptedPropertyEncrypted)
            ->getMock()
        ;
        //make sure things are cached
        $this->fieldEncryptor->encryptField($entity1, $reflectionProperty1);
        $this->fieldEncryptor->encryptField($entity2, $reflectionProperty1, 'foo');
        $this->assertEquals($entity1EncryptedPropertyEncrypted, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedPropertyEncrypted, $entity2->encryptedProperty);
        $this->fieldEncryptor->decryptField($entity1, $reflectionProperty1);
        $this->fieldEncryptor->decryptField($entity2, $reflectionProperty1, 'foo');
        $this->assertEquals($entity1EncryptedProperty, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedProperty, $entity2->encryptedProperty);
        $this->fieldEncryptor->encryptField($entity1, $reflectionProperty1);
        $this->fieldEncryptor->encryptField($entity2, $reflectionProperty1, 'foo');
        $this->assertEquals($entity1EncryptedPropertyEncrypted, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedPropertyEncrypted, $entity2->encryptedProperty);
        
        //make sure cache is cleared
        $this->fieldEncryptor->clearFieldCache();
        $this->fieldEncryptor->decryptField($entity1, $reflectionProperty1);
        $this->fieldEncryptor->decryptField($entity2, $reflectionProperty1, 'foo');
        $this->assertEquals($entity1EncryptedProperty, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedProperty, $entity2->encryptedProperty);

        //make sure using a different profile doesnt use the cache form the other profile
        $this->fieldEncryptor->encryptField($entity2, $reflectionProperty1, 'bar');
        $this->assertEquals($entity2EncryptedPropertyEncrypted, $entity2->encryptedProperty);
        $this->fieldEncryptor->decryptField($entity2, $reflectionProperty1, 'bar');
        $this->assertEquals($entity2EncryptedProperty, $entity2->encryptedProperty);

        //make sure values are ignored
        $this->fieldEncryptor->encryptField($entity1, $reflectionProperty1, null, [$entity1->encryptedProperty]);
        $this->fieldEncryptor->encryptField($entity2, $reflectionProperty1, 'foo', [$entity2->encryptedProperty]);
        $this->assertEquals($entity1EncryptedProperty, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedProperty, $entity2->encryptedProperty);
        $this->fieldEncryptor->decryptField($entity1, $reflectionProperty1, null, [$entity1->encryptedProperty]);
        $this->fieldEncryptor->decryptField($entity2, $reflectionProperty1, 'foo', [$entity2->encryptedProperty]);
        $this->assertEquals($entity1EncryptedProperty, $entity1->encryptedProperty);
        $this->assertEquals($entity2EncryptedProperty, $entity2->encryptedProperty);
    }
}
