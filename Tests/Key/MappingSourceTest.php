<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:34 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\MappingSource;

class MappingSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $key1 = $faker->unique()->word;
        $key2 = $faker->unique()->word;
        $key3 = $faker->unique()->word;
        $key4 = $faker->unique()->word;
        $mapKey1 = $faker->unique()->word;
        $mapKey2 = $faker->unique()->word;
        $keys = [
            $key1 => $faker->unique()->word,
            $key2 => $faker->unique()->word,
            $key3 => $faker->unique()->word,
            $key4 => $faker->unique()->word,
        ];
        $map = [
            $mapKey1 => $key3,
            $mapKey2 => $key1,
        ];
        $existingKeys = [$key1 => $keys[$key1], $mapKey1 => $keys[$key3], $mapKey2 => $keys[$key1]];
        return [
            [
                new MappingSource($map, new ArraySource($keys)),
                $existingKeys,
                [$faker->unique()->word, $faker->unique()->word]
            ]
        ];
    }
}
