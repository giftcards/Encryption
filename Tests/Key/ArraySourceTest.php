<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 6:27 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Omni\Encryption\Key\ArraySource;

class ArraySourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = array(
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        );
        return array(
            array(new ArraySource($keys), $keys, array($faker->unique()->word, $faker->unique()->word))
        );
    }
}
