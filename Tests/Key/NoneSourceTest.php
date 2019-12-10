<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:20 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\NoneSource;

class NoneSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        return [
            [
                new NoneSource(),
                ['none' => ''],
                [$faker->unique()->word, $faker->unique()->word]
            ]
        ];
    }
}
