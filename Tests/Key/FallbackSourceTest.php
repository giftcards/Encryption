<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:44 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\FallbackSource;

class FallbackSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $key1 = $faker->unique()->word;
        $key2 = $faker->unique()->word;
        $key3 = $faker->unique()->word;
        $key4 = $faker->unique()->word;
        $fallbackKey1 = $faker->unique()->word;
        $fallbackKey2 = $faker->unique()->word;
        $keys = [
            $key1 => $faker->unique()->word,
            $key2 => $faker->unique()->word,
            $key3 => $faker->unique()->word,
            $key4 => $faker->unique()->word,
        ];
        $fallbacks = [
            $key4 => [
                $faker->unique()->word,
                $key3
            ],
            $fallbackKey1 => [
                $faker->unique()->word,
                $key3
            ],
            $fallbackKey2 => [
                $key1,
                $faker->unique()->word
            ]
        ];
        $existingKeys = [$key4 => $keys[$key4], $fallbackKey1 => $keys[$key3], $fallbackKey2 => $keys[$key1]];
        return [
            [
                new FallbackSource($fallbacks, new ArraySource($keys)),
                $existingKeys,
                [$faker->unique()->word, $faker->unique()->word]
            ]
        ];
    }
}
