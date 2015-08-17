<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:59 PM
 */

namespace Omni\Encryption\Tests\CipherText;

use Faker\Generator;
use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\Group;
use Omni\Encryption\Profile\Profile;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class GroupTest extends AbstractExtendableTestCase
{
    public function testGettersAndArrayManagement()
    {
        /** @var Generator $faker */
        $faker = $this->getFaker();
        $id = $faker->unique()->word;
        $name1 = $faker->unique()->word;
        $name2 = $faker->unique()->word;
        $name3 = $faker->unique()->word;
        $cipherText1 = new CipherText(
            $faker->unique()->sentence(),
            new Profile(
                $faker->unique()->word,
                $faker->unique()->word
            )
        );
        $cipherText2 = new CipherText(
            $faker->unique()->sentence(),
            new Profile(
                $faker->unique()->word,
                $faker->unique()->word
            )
        );
        $cipherText3 = new CipherText(
            $faker->unique()->sentence(),
            new Profile(
                $faker->unique()->word,
                $faker->unique()->word
            )
        );
        $group = new Group(
            $id,
            array(
                $name1 => $cipherText1,
                $name2 => $cipherText2,
                $name3 => $cipherText3,
            )
        );
        $this->assertEquals($id, $group->getId());
        $this->assertSame($group->getCipherTexts(), iterator_to_array($group));
        $this->assertTrue(isset($group[$name1]));
        $this->assertSame($cipherText1, $group[$name1]);
        $this->assertTrue(isset($group[$name2]));
        $this->assertSame($cipherText2, $group[$name2]);
        $this->assertTrue(isset($group[$name3]));
        $this->assertSame($cipherText3, $group[$name3]);
        $this->assertFalse(isset($group[$faker->unique()->word]));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testOffsetSet()
    {
        $group = new Group($this->getFaker()->word, array());
        $group[$this->getFaker()->word] = $this->getFaker()->word;
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testOffsetUnset()
    {
        $group = new Group($this->getFaker()->word, array());
        unset($group[$this->getFaker()->word]);
    }
}
