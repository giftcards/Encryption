<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 6/4/15
 * Time: 2:55 PM
 */

namespace Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as AbstractAnnotationDriver;

class AnnotationDriver extends AbstractAnnotationDriver
{
    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string $className
     * @param ClassMetadata $metadata
     *
     * @return void
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /* @var $metadata \Doctrine\ORM\Mapping\ClassMetadataInfo */
        $metadata->hasEncryptedFields = false;
        $metadata->encryptedFields = array();
        $class = $metadata->getReflectionClass();
        
        foreach ($class->getProperties() as $name => $property) {
            /** @var \Giftcards\Encryption\Doctrine\Configuration\Annotation\Encrypted $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                'Giftcards\Encryption\Doctrine\Configuration\Annotation\Encrypted'
            );
            
            if (!$annotation) {
                continue;
            }
            $metadata->hasEncryptedFields = true;
            $metadata->encryptedFields[$property->getName()] = array(
                'profile' => $annotation->profile,
            );
        }
    }
}
