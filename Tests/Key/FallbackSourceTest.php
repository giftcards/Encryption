<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/26/15
 * Time: 5:44 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Omni\Encryption\Key\ArraySource;
use Omni\Encryption\Key\FallbackSource;

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
        $keys = array(
            $key1 => $faker->unique()->word,
            $key2 => $faker->unique()->word,
            $key3 => $faker->unique()->word,
            $key4 => $faker->unique()->word,
        );
        $fallbacks = array(
            $fallbackKey1 => array(
                $faker->unique()->word,
                $key3
            ),
            $fallbackKey2 => array(
                $key1,
                $faker->unique()->word
            )
        );
        $existingKeys = array_merge($keys, array($fallbackKey1 => $keys[$key3], $fallbackKey2 => $keys[$key1]));
        return array(
            array(
                new FallbackSource($fallbacks, new ArraySource($keys)),
                $existingKeys,
                array($faker->unique()->word, $faker->unique()->word)
            )
        );
    }
}
