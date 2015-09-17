<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/17/15
 * Time: 1:19 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Giftcards\Encryption\Key\ArraySource;
use Giftcards\Encryption\Key\ContainerAwareChainSource;
use Symfony\Component\DependencyInjection\Container;

class ContainerAwareChainSourceTest extends AbstractSourceTest
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
        $container = new Container();
        $container
            ->set('source2', $source2)
        ;
        $chain = new ContainerAwareChainSource($container);
        $this->assertSame(
            $chain,
            $chain
                ->add($source1)
                ->addServiceId('source2')
                ->add($source3)
        );
        return array(
            array($chain, $keys, array($faker->unique()->word, $faker->unique()->word))
        );
    }
}
