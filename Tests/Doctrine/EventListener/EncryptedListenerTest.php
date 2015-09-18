<?php
namespace Giftcards\Encryption\Tests\Doctrine\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver\AnnotationDriver;
use Giftcards\Encryption\Doctrine\EventListener\EncryptedListener;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties;
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
        $metadata1 = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties');
        $metadata1->reflClass = new \ReflectionClass($metadata1->getName());
        $metadata1->reflFields['encryptedProperty'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties',
            'encryptedProperty'
        );
        $metadata1->reflFields['encryptedProperty']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata1->getName(), $metadata1);
        $metadata2 = new ClassMetadataInfo(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet'
        );
        $metadata2->reflClass = new \ReflectionClass($metadata2->getName());
        $metadata2->reflFields['encryptedProperty'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet',
            'encryptedProperty'
        );
        $metadata2->reflFields['encryptedProperty']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata2->getName(), $metadata2);
        $metadata3 = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties');
        $metadata3->reflClass = new \ReflectionClass($metadata3->getName());
        $this->driver->loadMetadataForClass($metadata3->getName(), $metadata3);

        $entity1EncryptedProperty = $this->getFaker()->unique()->word;
        $entity1EncryptedPropertyEncrypted = $this->getFaker()->unique()->word;
        $entity3EncryptedProperty = $this->getFaker()->unique()->word;
        $entity3EncryptedPropertyEncrypted = $this->getFaker()->unique()->word;
        
        $entity1 = new MockEntityWithEncryptedProperties();
        $entity1->encryptedProperty = $entity1EncryptedProperty;
        $entity1->normalProperty = $this->getFaker()->unique()->word;
        $clonedEntity1 = clone $entity1;
        $entity2 = new MockEntityWithoutEncryptedProperties();
        $entity2->normalProperty = $this->getFaker()->unique()->word;
        $entity2->otherNormalProperty = $this->getFaker()->unique()->word;
        $clonedEntity2 = clone $entity2;
        $entity3 = new MockEntityWithEncryptedPropertiesAndProfileSet();
        $entity3->encryptedProperty = $entity3EncryptedPropertyEncrypted;
        $entity3->normalProperty = $this->getFaker()->unique()->word;
        $clonedEntity3 = clone $entity3;
        $clonedEntity3->encryptedProperty = $entity3EncryptedProperty;
        $entity4 = new MockEntityWithoutEncryptedProperties();
        $entity4->normalProperty = $this->getFaker()->unique()->word;
        $entity4->otherNormalProperty = $this->getFaker()->unique()->word;
        $clonedEntity4 = clone $entity4;

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager')
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties')
            ->andReturn($metadata1)
            ->getMock()
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet')
            ->andReturn($metadata2)
            ->getMock()
            ->shouldReceive('getClassMetadata')
            ->with('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties')
            ->andReturn($metadata3)
            ->getMock()
        ;
        $this->encryptor
            ->shouldReceive('encrypt')
            ->with($entity1EncryptedProperty, null)
            ->andReturn($entity1EncryptedPropertyEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity1EncryptedPropertyEncrypted, null)
            ->andReturn($entity1EncryptedProperty)
            ->getMock()
            ->shouldReceive('encrypt')
            ->with($entity3EncryptedProperty, 'foo')
            ->andReturn($entity3EncryptedPropertyEncrypted)
            ->getMock()
            ->shouldReceive('decrypt')
            ->with($entity3EncryptedPropertyEncrypted, 'foo')
            ->andReturn($entity3EncryptedProperty)
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
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties');
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(new LoadClassMetadataEventArgs($metadata, $entityManager));
        
        $this->assertTrue($metadata->hasEncryptedProperties);
        $this->assertEquals(array(
            'encryptedProperty' => array(
                'profile' => null,
            )
        ), $metadata->encryptedProperties);
        
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties');
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(new LoadClassMetadataEventArgs($metadata, $entityManager));
        $this->assertFalse($metadata->hasEncryptedProperties);
        $this->assertEquals(array(), $metadata->encryptedProperties);
    }
}
