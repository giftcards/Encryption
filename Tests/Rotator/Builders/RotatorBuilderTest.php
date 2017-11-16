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
use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Tests\AbstractTestCase;

class RotatorBuilderTest extends AbstractTestCase
{
    public function testBuilder()
    {
        $builderName = $this->getFaker()->unique()->word;
        $builder = \Mockery::mock(BuilderInterface::class);
        $builder->shouldReceive("getName")->andReturn($builderName);

        $rotatorBuilder = new RotatorBuilder([$builder]);
        $rotatorBuilder->build();

        $builder->shouldHaveReceived("getName");
    }
}