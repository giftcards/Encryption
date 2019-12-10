<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:49 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\CombiningSource;

class CombiningSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = [
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        ];
        $keysKeys = array_keys($keys);
        $combinedKeys = [
            $faker->unique()->word => $keys[$keysKeys[1]].$keys[$keysKeys[3]],
            $faker->unique()->word => $keys[$keysKeys[0]].$keys[$keysKeys[5]],
            $faker->unique()->word => $keys[$keysKeys[4]].$keys[$keysKeys[2]],
            $faker->unique()->word => $keys[$keysKeys[1]].$keys[$keysKeys[1]],
        ];
        $combinedKeysKeys = array_keys($combinedKeys);
        $missingKey1 = $faker->unique()->word;
        $missingKey2 = $faker->unique()->word;
        $missingKey3 = $faker->unique()->word;
        $source = new CombiningSource(
            [
                $combinedKeysKeys[0] => [
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $keysKeys[3]
                ],
                $combinedKeysKeys[1] => [
                    CombiningSource::LEFT => $keysKeys[0],
                    CombiningSource::RIGHT => $keysKeys[5]
                ],
                $combinedKeysKeys[2] => [
                    CombiningSource::LEFT => $keysKeys[4],
                    CombiningSource::RIGHT => $keysKeys[2]
                ],
                $combinedKeysKeys[3] => [
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $keysKeys[1]
                ],
                $missingKey1 => [
                    CombiningSource::LEFT => $keysKeys[1],
                    CombiningSource::RIGHT => $faker->unique()->word
                ],
                $missingKey2 => [
                    CombiningSource::LEFT => $faker->unique()->word,
                    CombiningSource::RIGHT => $faker->unique()->word
                ],
                $missingKey3 => [
                    CombiningSource::LEFT => $faker->unique()->word,
                    CombiningSource::RIGHT => $keysKeys[3]
                ],
            ],
            new ArraySource($keys)
        );
        return [
            [$source, $combinedKeys, [$missingKey1, $missingKey2, $missingKey3]]
        ];
    }

    public function testCOnstrustorWhenLeftAndRightArgNotFormattedCorrectly()
    {
        $this->expectException('\InvalidArgumentException');
        $leftAndRight = [
            $this->getFaker()->unique()->word => ['sdfdsf']
        ];
        new CombiningSource(
            $leftAndRight,
            new ArraySource([])
        );
    }
}
