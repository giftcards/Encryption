<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 8:04 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\MongoSource;
use Mockery;

class MongoSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = [
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        ];
        $database = $faker->unique()->word;
        $collectionName = $faker->unique()->word;
        $findByField = $faker->unique()->word;
        $valueField = $faker->unique()->word;
        $missingKeys = [$faker->unique()->word, $faker->unique()->word];

        $collection = Mockery::mock('Doctrine\MongoDB\Collection');
        $connection = Mockery::mock('Doctrine\MongoDB\Connection')
            ->shouldReceive('selectCollection')
            ->with($database, $collectionName)
            ->andReturn($collection)
            ->getMock()
        ;

        foreach ($keys as $name => $value) {
            $collection
                ->shouldReceive('findOne')
                ->with([$findByField => $name])
                ->andReturn([$valueField => $value])
            ;
        }

        foreach ($missingKeys as $name => $value) {
            $collection
                ->shouldReceive('findOne')
                ->with([$findByField => $name])
                ->andReturnNull()
            ;
        }


        return [
            [
                new MongoSource(
                    $connection,
                    $database,
                    $collectionName,
                    $findByField,
                    $valueField
                ),
                $keys,
                $missingKeys
            ]
        ];
    }
}
