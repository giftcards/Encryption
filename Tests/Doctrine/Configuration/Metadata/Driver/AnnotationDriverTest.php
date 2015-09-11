<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 6/5/15
 * Time: 12:34 PM
 */

namespace Giftcards\Encryption\Tests\Doctrine\Configuration\Metadata\Driver;

use Giftcards\Encryption\Doctrine\Configuration\Metadata\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Giftcards\Encryption\Tests\AbstractTestCase;

class AnnotationDriverTest extends AbstractTestCase
{
    /** @var  AnnotationDriver */
    protected $driver;

    public function setUp()
    {
        $this->driver = new AnnotationDriver(new AnnotationReader());
    }

    public function testLoadMetadataForClass()
    {
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFields');
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
        $this->assertTrue($metadata->hasEncryptedFields);
        $this->assertEquals(array('encryptedField' => array(
            'profile' => null,
        )), $metadata->encryptedFields);
        $metadata = new ClassMetadataInfo(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedFieldsAndProfileSet'
        );
        $metadata->reflClass = new \ReflectionClass($metadata->getName());

        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
        $this->assertTrue($metadata->hasEncryptedFields);
        $this->assertEquals(array('encryptedField' => array(
            'profile' => 'foo',
        )), $metadata->encryptedFields);
    }
}
