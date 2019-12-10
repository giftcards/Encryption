<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 8:13 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\PrefixKeyNameSource;

class PrefixKeyNameSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $prefix = $faker->unique()->word;
        $separator = $faker->unique()->word;
        $keys = [
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        ];
        $prefixedKeys = array_combine(
            array_map(function ($keyKey) use ($prefix, $separator) {
                return $prefix.$separator.$keyKey;
            }, array_keys($keys)),
            $keys
        );
        return [
            [
                new PrefixKeyNameSource($prefix, new ArraySource($keys), $separator),
                $prefixedKeys,
                array_keys($keys)
            ],
            [
                new PrefixKeyNameSource($prefix, new ArraySource($keys), $separator),
                $prefixedKeys,
                [$faker->unique()->word, $faker->unique()->word]
            ]
        ];
    }
}
