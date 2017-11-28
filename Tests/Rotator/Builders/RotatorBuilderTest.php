<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 9:34 AM
 */

namespace Giftcards\Encryption\Tests\Rotator\Builders;

use Giftcards\Encryption\CipherText\Rotator\RotatorBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class RotatorBuilderTest extends AbstractTestCase
{
    public function testBuilder()
    {
        $builderName = $this->getFaker()->unique()->word;
        $builder = \Mockery::mock("Giftcards\\Encryption\\Factory\\BuilderInterface");
        $builder->shouldReceive("getName")->andReturn($builderName);

        $rotatorBuilder = new RotatorBuilder(array($builder));
        $rotatorBuilder->build();

        $builder->shouldHaveReceived("getName");
    }
}
