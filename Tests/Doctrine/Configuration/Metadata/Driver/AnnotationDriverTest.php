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
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use ReflectionClass;

class AnnotationDriverTest extends AbstractExtendableTestCase
{
    /** @var  AnnotationDriver */
    protected $driver;

    public function setUp() : void
    {
        $this->driver = new AnnotationDriver(new AnnotationReader());
    }

    public function testLoadMetadataForClass()
    {
        $metadata = new ClassMetadataInfo('Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedProperties');
        $metadata->reflClass = new ReflectionClass($metadata->getName());

        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
        $this->assertTrue($metadata->hasEncryptedProperties);
        $this->assertEquals([
            'encryptedProperty' => [
            'profile' => null,
            'ignored_values' => [null]
            ]
        ], $metadata->encryptedProperties);
        $metadata = new ClassMetadataInfo(
            'Giftcards\Encryption\Tests\Doctrine\MockEntityWithEncryptedPropertiesAndProfileSet'
        );
        $metadata->reflClass = new ReflectionClass($metadata->getName());

        $this->driver->loadMetadataForClass($metadata->getName(), $metadata);
        $this->assertTrue($metadata->hasEncryptedProperties);
        $this->assertEquals([
            'encryptedProperty' => [
            'profile' => 'foo',
            'ignored_values' => [null, 'sdf']
            ]
        ], $metadata->encryptedProperties);
    }
}
