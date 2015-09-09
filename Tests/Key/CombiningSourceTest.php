<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:49 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Omni\Encryption\Key\ArraySource;
use Omni\Encryption\Key\CombiningSource;

class CombiningSourceTest extends AbstractSourceTest
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
        $keysKeys = array_keys($keys);
        $combinedKeys = array(
            $faker->unique()->word => $keys[$keysKeys[1]].$keys[$keysKeys[3]],
            $faker->unique()->word => $keys[$keysKeys[0]].$keys[$keysKeys[5]],
            $faker->unique()->word => $keys[$keysKeys[4]].$keys[$keysKeys[2]],
            $faker->unique()->word => $keys[$keysKeys[1]].$keys[$keysKeys[1]],
        );
        $combinedKeysKeys = array_keys($combinedKeys);
        $missingKey1 = $faker->unique()->word;
        $missingKey2 = $faker->unique()->word;
        $missingKey3 = $faker->unique()->word;
        $source = new CombiningSource(
            array(
                $combinedKeysKeys[0] => array(
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $keysKeys[3]
                ),
                $combinedKeysKeys[1] => array(
                    CombiningSource::LEFT => $keysKeys[0],
                    CombiningSource::RIGHT => $keysKeys[5]
                ),
                $combinedKeysKeys[2] => array(
                    CombiningSource::LEFT => $keysKeys[4],
                    CombiningSource::RIGHT => $keysKeys[2]
                ),
                $combinedKeysKeys[3] => array(
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $keysKeys[1]
                ),
                $missingKey1 => array(
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $faker->unique()->word
                ),
                $missingKey2 => array(
                    CombiningSource::LEFT => $faker->unique()->word,
                    CombiningSource::RIGHT => $faker->unique()->word
                ),
                $missingKey3 => array(
                    CombiningSource::LEFT => $faker->unique()->word,
                    CombiningSource::RIGHT => $keysKeys[3]
                ),
            ),
            new ArraySource($keys)
        );
        return array(
            array($source, $combinedKeys, array($missingKey1, $missingKey2, $missingKey3))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCOnstrustorWhenLeftAndRightArgNotFormattedCorrectly()
    {
        $leftAndRight = array(
            $this->getFaker()->unique()->word => array('sdfdsf')
        );
        new CombiningSource(
            $leftAndRight,
            new ArraySource(array())
        );
    }
}
