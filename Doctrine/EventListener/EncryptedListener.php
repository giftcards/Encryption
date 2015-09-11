<?php
namespace Giftcards\Encryption\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Giftcards\Encryption\Encryptor;

class EncryptedListener implements EventSubscriber
{
    protected $encryptor;
    protected $driver;
    protected $entities = array();

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::postLoad,
            Events::preFlush,
            Events::postFlush,
            Events::onClear,
            Events::loadClassMetadata,
        );
    }

    public function __construct(Encryptor $encryptor, MappingDriver $driver)
    {
        $this->encryptor = $encryptor;
        $this->driver = $driver;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        $classMetadata = $event->getEntityManager()
            ->getClassMetadata(get_class($entity))
        ;

        if (!empty($classMetadata->hasEncryptedFields)) {
            $this->entities[] = $entity;
        }
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        $classMetadata = $event->getEntityManager()->getClassMetadata(
            get_class($entity)
        );
        if (!empty($classMetadata->hasEncryptedFields)) {
            $this->entities[] = $entity;
            $this->decrypt(array($entity), $event->getEntityManager());
        }
    }

    public function preFlush(PreFlushEventArgs $event)
    {
        $this->encrypt($this->entities, $event->getEntityManager());
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $this->decrypt($this->entities, $event->getEntityManager());
    }

    public function onClear()
    {
        $this->entities = array();
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
    }

    /**
     * @param array $entities
     * @param $entityManager
     */
    protected function encrypt(array $entities, EntityManager $entityManager)
    {
        foreach ($entities as $entity) {
            /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
            $metadata = $entityManager->getClassMetadata(get_class($entity));

            foreach ($metadata->encryptedFields as $name => $options) {
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
     * @param $entityManager
     */
    protected function decrypt(array $entities, EntityManager $entityManager)
    {
        foreach ($entities as $entity) {
            /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
            $metadata = $entityManager->getClassMetadata(get_class($entity));

            foreach ($metadata->encryptedFields as $name => $options) {
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
}
