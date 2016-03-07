<?php
namespace Giftcards\Encryption\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs as ODMLifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs as ODMPostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs as ODMPreFlushEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs as ORMLifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs as ORMPostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs as ORMPreFlushEventArgs;
use Giftcards\Encryption\Encryptor;

class EncryptedListener implements EventSubscriber
{
    protected $encryptor;
    protected $driver;
    protected $entities = array();

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postLoad',
            'preFlush',
            'postFlush',
            'onClear',
            'loadClassMetadata',
        );
    }

    public function __construct(Encryptor $encryptor, MappingDriver $driver)
    {
        $this->encryptor = $encryptor;
        $this->driver = $driver;
    }

    public function prePersist($event)
    {
        $entity = $this->getObject($event);

        $classMetadata = $this->getObjectManager($event)->getClassMetadata(get_class($entity));

        if (!empty($classMetadata->hasEncryptedProperties) && array_search($entity, $this->entities, true) === false) {
            $this->entities[] = $entity;
        }
    }

    public function postLoad($event)
    {
        $entity = $this->getObject($event);

        $objectManager = $this->getObjectManager($event);
        if (!empty($objectManager->getClassMetadata(get_class($entity))->hasEncryptedProperties)) {
            if (array_search($entity, $this->entities, true) === false) {
                $this->entities[] = $entity;
            }
            $this->decrypt(array($entity), $objectManager);
        }
    }

    public function preFlush($event)
    {
        $this->encrypt($this->entities, $this->getObjectManager($event));
    }

    public function postFlush($event)
    {
        $this->decrypt($this->entities, $this->getObjectManager($event));
    }

    public function onClear()
    {
        $this->entities = array();
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
    }

    /**
     * @param array $entities
     * @param EntityManager|DocumentManager $objectManager
     */
    protected function encrypt(array $entities, $objectManager)
    {
        foreach ($entities as $entity) {
            /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
            $metadata = $objectManager->getClassMetadata(get_class($entity));

            foreach ($metadata->encryptedProperties as $name => $options) {
                $metadata->reflFields[$name]->setValue(
                    $entity,
                    $this->encryptor->encrypt(
                        $metadata->reflFields[$name]->getValue($entity),
                        $options['profile']
                    )
                );
            }
        }
    }

    /**
     * @param array $entities
     * @param EntityManager|DocumentManager $objectManager
     */
    protected function decrypt(array $entities, $objectManager)
    {
        foreach ($entities as $entity) {
            /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
            $metadata = $objectManager->getClassMetadata(get_class($entity));

            foreach ($metadata->encryptedProperties as $name => $options) {
                $metadata->reflFields[$name]->setValue(
                    $entity,
                    $this->encryptor->decrypt(
                        $metadata->reflFields[$name]->getValue($entity),
                        $options['profile']
                    )
                );
            }
        }
    }

    /**
     * @param $event
     * @return DocumentManager|EntityManager
     */
    protected function getObjectManager($event)
    {
        if (
            $event instanceof ORMLifecycleEventArgs
            || $event instanceof ORMPreFlushEventArgs
            || $event instanceof ORMPostFlushEventArgs
        ) {
            return $event->getEntityManager();
        }
        
        if (
            $event instanceof ODMLifecycleEventArgs
            || $event instanceof ODMPreFlushEventArgs
            || $event instanceof ODMPostFlushEventArgs
        ) {
            return $event->getDocumentManager();
        }
    }

    /**
     * @param $event
     * @return object
     */
    protected function getObject($event)
    {
        if ($event instanceof ORMLifecycleEventArgs) {
            return $event->getEntity();
        }
        
        if ($event instanceof ODMLifecycleEventArgs) {
            return $event->getDocument();
        }
    }
}
