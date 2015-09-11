<?php
namespace Giftcards\Encryption\Tests\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver\AnnotationDriver;
use Giftcards\Encryption\Doctrine\EventListener\EncryptedListener;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFieldsAndProfileSet;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedFields;
use Giftcards\Encryption\Tests\Doctrine\MockEncryptedEntity;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Mockery\MockInterface;

class EncryptedListenerTest extends AbstractTestCase
{
    /** @var EncryptedListener */
    protected $listener;
    /** @var  MockInterface */
    protected $encryptor;
    /** @var  AnnotationDriver */
    protected $driver;

    public function setUp()
    {
        $this->listener = new EncryptedListener(
            $this->encryptor = \Mockery::mock('Giftcards\Encryption\Encryptor'),
            $this->driver = new AnnotationDriver(new AnnotationReader())
        );
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            $this->listener->getSubscribedEvents(),
            array(
                Events::prePersist,
                Events::postLoad,
                Events::preFlush,
                Events::postFlush,
                Events::onClear,
                Events::loadClassMetadata,
            )
        );
    }

    public function testLifeCycleWithNoErrors()
    {
        $metadata1 = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields');
        $metadata1->reflClass = new \ReflectionClass($metadata1->getName());
        $metadata1->reflFields['encryptedField'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields',
            'encryptedField'
        );
        $metadata1->reflFields['encryptedField']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata1->getName(), $metadata1);
        $metadata2 = new ClassMetadataInfo(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFieldsAndProfileSet'
        );
        $metadata2->reflClass = new \ReflectionClass($metadata2->getName());
        $metadata2->reflFields['encryptedField'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFieldsAndProfileSet',
            'encryptedField'
        );
        $metadata2->reflFields['encryptedField']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata2->getName(), $metadata2);
        $metadata3 = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedFields');
        $metadata3->reflClass = new \ReflectionClass($metadata3->getName());
        $this->driver->loadMetadataForClass($metadata3->getName(), $metadata3);

        $entity1EncryptedField = $this->getFaker()->unique()->word;
        $entity1EncryptedFieldEncrypted = $this->getFaker()->unique()->word;
        $entity3EncryptedField = $this->getFaker()->unique()->word;
        $entity3EncryptedFieldEncrypted = $this->getFaker()->unique()->word;
        
        $entity1 = new MockEntityWithEncryptedFields();
        $entity1->encryptedField = $entity1EncryptedField;
        $entity1->normalField = $this->getFaker()->unique()->word;
        $clonedEntity1 = clone $entity1;
        $entity2 = new MockEntityWithoutEncryptedFields();
        $entity2->normalField = $this->getFaker()->unique()->word;
        $entity2->otherNormalField = $this->getFaker()->unique()->word;
        $clonedEntity2 = clone $entity2;
        $entity3 = new MockEntityWithEncryptedFieldsAndProfileSet();
        $entity3->encryptedField = $entity3EncryptedFieldEncrypted;
        $entity3->normalField = $this->getFaker()->unique()->word;
        $clonedEntity3 = clone $entity3;
        $clonedEntity3->encryptedField = $entity3EncryptedField;
        $entity4 = new MockEntityWithoutEncryptedFields();
        $entity4->normalField = $this->getFaker()->unique()->word;
        $entity4->otherNormalField = $this->getFaker()->unique()->word;
        $clonedEntity4 = clone $entity4;

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager')
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields')
            ->andReturn($metadata1)
            ->getMock()
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFieldsAndProfileSet')
            ->andReturn($metadata2)
            ->getMock()
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedFields')
            ->andReturn($metadata3)
            ->getMock()
        ;
        $this->encryptor
            ->shouldReceive('encrypt')
            ->with($entity1EncryptedField, null)
            ->andReturn($entity1EncryptedFieldEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity1EncryptedFieldEncrypted, null)
            ->andReturn($entity1EncryptedField)
            ->getMock()
            ->shouldReceive('encrypt')
            ->with($entity3EncryptedField, 'foo')
            ->andReturn($entity3EncryptedFieldEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity3EncryptedFieldEncrypted, 'foo')
            ->andReturn($entity3EncryptedField)
            ->getMock()
        ;
        
        $this->listener->prePersist(new LifecycleEventArgs($entity1, $entityManager));
        $this->listener->prePersist(new LifecycleEventArgs($entity2, $entityManager));
        $this->listener->postLoad(new LifecycleEventArgs($entity3, $entityManager));
        $this->listener->postLoad(new LifecycleEventArgs($entity4, $entityManager));
        $this->listener->preFlush(new PreFlushEventArgs($entityManager));
        $this->listener->postFlush(new PostFlushEventArgs($entityManager));
        $this->listener->onClear();
        $this->listener->preFlush(new PreFlushEventArgs($entityManager));
        $this->assertEquals($clonedEntity1, $entity1);
        $this->assertEquals($clonedEntity2, $entity2);
        $this->assertEquals($clonedEntity3, $entity3);
        $this->assertEquals($clonedEntity4, $entity4);
    }

    public function testLoadClassMetadata()
    {
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields');
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(new LoadClassMetadataEventArgs($metadata, $entityManager));
        
        $this->assertTrue($metadata->hasEncryptedFields);
        $this->assertEquals(array(
            'encryptedField' => array(
                'profile' => null,
            )
        ), $metadata->encryptedFields);
        
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedFields');
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(new LoadClassMetadataEventArgs($metadata, $entityManager));
        $this->assertFalse($metadata->hasEncryptedFields);
        $this->assertEquals(array(), $metadata->encryptedFields);
    }
}
