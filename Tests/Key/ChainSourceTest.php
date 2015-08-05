<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:43 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Omni\Encryption\Key\ArraySource;
use Omni\Encryption\Key\ChainSource;

class ChainSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = array(
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        );
        $keyChunks = array_chunk($keys, 2, true);
        $source1 = new ArraySource($keyChunks[0]);
        $source2 = new ArraySource($keyChunks[1]);
        $source3 = new ArraySource($keyChunks[2]);
        $chain = new ChainSource();
        $this->assertSame(
            $chain,
            $chain
                ->add($source1)
                ->add($source2)
                ->add($source3)
        );
        return array(
            array($chain, $keys, array($faker->unique()->word, $faker->unique()->word))
        );
    }
}
