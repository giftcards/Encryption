<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 9:34 AM
 */

namespace Giftcards\Encryption\Tests\Rotator\Builders;

use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\RotatorBuilder;
use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;
use Giftcards\Encryption\Encryptor;
use Giftcards\Encryption\Tests\AbstractTestCase;

class RotatorBuilderTest extends AbstractTestCase
{
    public function testBuilder()
    {
        $encryptor = \Mockery::mock(Encryptor::class);
        $storeRegistry = \Mockery::mock(StoreRegistry::class);
        $builder = new RotatorBuilder();
        assert($encryptor instanceof Encryptor);
        assert($storeRegistry instanceof StoreRegistry);
        $this->assertEquals(new Rotator($encryptor, $storeRegistry), $builder->build([
            'encryptor' => $encryptor,
            'storeRegistry' => $storeRegistry
        ]));
    }
}