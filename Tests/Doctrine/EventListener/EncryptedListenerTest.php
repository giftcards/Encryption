<?php
namespace Giftcards\Encryption\Tests\Doctrine\EventListener;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo as ODMClassMetadataInfo;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver\AnnotationDriver;
use Giftcards\Encryption\Doctrine\EventListener\EncryptedListener;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet;
use Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo as ORMClassMetadataInfo;
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
                'prePersist',
                'postLoad',
                'preFlush',
                'postFlush',
                'onClear',
                'loadClassMetadata',
            )
        );
    }

    /**
     * @dataProvider useOrmProvider
     */
    public function testLifeCycleWithNoErrors($orm)
    {
        $metadata1 = $this->getClassMetadata(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties',
            $orm
        );
        $metadata1->reflClass = new \ReflectionClass($metadata1->getName());
        $metadata1->reflFields['encryptedProperty'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties',
            'encryptedProperty'
        );
        $metadata1->reflFields['encryptedProperty']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata1->getName(), $metadata1);
        $metadata2 = $this->getClassMetadata(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet',
            $orm
        );
        $metadata2->reflClass = new \ReflectionClass($metadata2->getName());
        $metadata2->reflFields['encryptedProperty'] = new \ReflectionProperty(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet',
            'encryptedProperty'
        );
        $metadata2->reflFields['encryptedProperty']->setAccessible(true);
        $this->driver->loadMetadataForClass($metadata2->getName(), $metadata2);
        $metadata3 = $this->getClassMetadata(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties',
            $orm
        );
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
        
        $this->listener->prePersist($this->getLifecycleEvent($entity1, $entityManager, $orm));
        $this->listener->prePersist($this->getLifecycleEvent($entity1, $entityManager, $orm));
        $this->listener->prePersist($this->getLifecycleEvent($entity2, $entityManager, $orm));
        $this->listener->postLoad($this->getLifecycleEvent($entity3, $entityManager, $orm));
        $entity3->encryptedProperty = $entity3EncryptedPropertyEncrypted;
        $this->listener->postLoad($this->getLifecycleEvent($entity3, $entityManager, $orm));
        $this->listener->postLoad($this->getLifecycleEvent($entity4, $entityManager, $orm));
        $this->listener->preFlush($this->getPreFlushEvent($entityManager, $orm));
        $this->listener->postFlush($this->getPostFlushEvent($entityManager, $orm));
        $this->listener->onClear();
        $this->listener->preFlush($this->getPreFlushEvent($entityManager, $orm));
        $this->assertEquals($clonedEntity1, $entity1);
        $this->assertEquals($clonedEntity2, $entity2);
        $this->assertEquals($clonedEntity3, $entity3);
        $this->assertEquals($clonedEntity4, $entity4);
    }

    /**
     * @dataProvider useOrmProvider
     */
    public function testLoadClassMetadata($orm)
    {
        $metadata = $this->getClassMetadata(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties',
            $orm
        );
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(
            $this->getLoadClassMetadataEvent($metadata, $entityManager, $orm)
        );
        
        $this->assertTrue($metadata->hasEncryptedProperties);
        $this->assertEquals(array(
            'encryptedProperty' => array(
                'profile' => null,
            )
        ), $metadata->encryptedProperties);
        
        $metadata = $this->getClassMetadata(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithoutEncryptedProperties',
            $orm
        );
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        /** @var MockInterface|EntityManager $entityManager */
        $entityManager = \Mockery::mock('Doctrine\ORM\EntityManager');

        $this->listener->loadClassMetadata(
            $this->getLoadClassMetadataEvent($metadata, $entityManager, $orm)
        );
        $this->assertFalse($metadata->hasEncryptedProperties);
        $this->assertEquals(array(), $metadata->encryptedProperties);
    }

    public function useOrmProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }

    /**
     * @return ORMClassMetadataInfo|ODMClassMetadataInfo
     */
    protected function getClassMetadata($class, $orm)
    {
        if ($orm) {
            return new ORMClassMetadataInfo($class);
        }
        
        return new ODMClassMetadataInfo($class);
    }

    /**
     * @param $entity1
     * @param $entityManager
     * @return LifecycleEventArgs
     */
    protected function getLifecycleEvent($entity, $entityManager, $orm)
    {
        if ($orm) {
            return new LifecycleEventArgs($entity, $entityManager);
        }
        
        return new \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs($entity, $entityManager);
    }

    /**
     * @param $entityManager
     * @return PreFlushEventArgs
     */
    protected function getPreFlushEvent($entityManager, $orm)
    {
        if ($orm) {
            return new PreFlushEventArgs($entityManager);
        }
        
        return new \Doctrine\ODM\MongoDB\Event\PreFlushEventArgs($entityManager);
    }

    /**
     * @param $orm
     * @param $entityManager
     * @return PostFlushEventArgs
     */
    protected function getPostFlushEvent($entityManager, $orm)
    {
        if ($orm) {
            return new PostFlushEventArgs($entityManager);
        }
        
        return new \Doctrine\ODM\MongoDB\Event\PostFlushEventArgs($entityManager);
    }

    /**
     * @param $metadata
     * @param $entityManager
     * @return LoadClassMetadataEventArgs
     */
    protected function getLoadClassMetadataEvent($metadata, $entityManager, $orm)
    {
        if ($orm) {
            return new LoadClassMetadataEventArgs($metadata, $entityManager);
        }
        
        return new \Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs($metadata, $entityManager);
    }
}
