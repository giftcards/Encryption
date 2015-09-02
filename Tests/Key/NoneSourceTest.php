<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:20 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Omni\Encryption\Key\NoneSource;

class NoneSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        return array(
            array(
                new NoneSource(),
                array('none' => ''),
                array($faker->unique()->word, $faker->unique()->word)
            )
        );
    }
}
