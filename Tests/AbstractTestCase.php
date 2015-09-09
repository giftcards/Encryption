<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 6:31 PM
 */

namespace Giftcards\Encryption\Tests;

use Faker\Factory;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected $faker;
    
    public function getFaker()
    {
        if (!$this->faker) {
            $this->faker = Factory::create();
        }
        
        return $this->faker;
    }
}
