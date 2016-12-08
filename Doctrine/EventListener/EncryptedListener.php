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
use Giftcards\Encryption\Doctrine\FieldEncryptor;
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

    public function __construct($encryptor, MappingDriver $driver)
    {
        if (!$encryptor instanceof Encryptor && !$encryptor instanceof FieldEncryptor) {
            throw new \InvalidArgumentException('$encryptor must be an instance of
                Giftcards\Encryption\Encryptor
                or Giftcards\Encryption\Doctrine\FieldEncryptor
            ');
        }
        
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
        if ($this->encryptor instanceof FieldEncryptor) {
            $this->encryptor->clearFieldCache();
        }
        $this->entities = array();
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs|LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata($eventArgs)
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
                $this->encryptField(
                    $entity,
                    $metadata->reflFields[$name],
                    $options
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
                $this->decryptField(
                    $entity,
                    $metadata->reflFields[$name],
                    $options
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

    protected function encryptField(
        $entity,
        \ReflectionProperty $field,
        array $options
    ) {
        if ($this->encryptor instanceof Encryptor) {
            $field->setValue(
                $entity,
                $this->encryptor->encrypt(
                    $field->getValue($entity),
                    $options['profile']

                )
            );
            return;
        }
        
        $this->encryptor->encryptField(
            $entity,
            $field,
            $options['profile'],
            $options['ignored_values']
        );
    }

    protected function decryptField(
        $entity,
        \ReflectionProperty $field,
        array $options
    ) {
        if ($this->encryptor instanceof Encryptor) {
            $field->setValue(
                $entity,
                $this->encryptor->decrypt(
                    $field->getValue($entity),
                    $options['profile']
                )
            );
            return;
        }

        $this->encryptor->decryptField(
            $entity,
            $field,
            $options['profile'],
            $options['ignored_values']
        );
    }
}
