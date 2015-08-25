<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 5:48 PM
 */

namespace Omni\Encryption\Tests\Profile;

use Omni\Encryption\Profile\Profile;
use Omni\Encryption\Profile\ProfileRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ProfileRegistryTest extends AbstractExtendableTestCase
{
    /** @var  ProfileRegistry */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ProfileRegistry();
    }

    public function testGettersSetters()
    {
        $profile1 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile1Name = $this->getFaker()->unique()->word;
        $profile2 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile2Name = $this->getFaker()->unique()->word;
        $profile3 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile3Name = $this->getFaker()->unique()->word;

        $this->registry
            ->set($profile1Name, $profile1)
            ->set($profile2Name, $profile2)
            ->set($profile3Name, $profile3)
        ;
        $this->assertTrue($this->registry->has($profile1Name));
        $this->assertSame($profile1, $this->registry->get($profile1Name));
        $this->assertTrue($this->registry->has($profile2Name));
        $this->assertSame($profile2, $this->registry->get($profile2Name));
        $this->assertTrue($this->registry->has($profile3Name));
        $this->assertSame($profile3, $this->registry->get($profile3Name));
        $this->assertSame(array(
            $profile1Name => $profile1,
            $profile2Name => $profile2,
            $profile3Name => $profile3,
        ), $this->registry->all());
    }

    /**
     * @expectedException \Omni\Encryption\Profile\ProfileNotFoundException
     */
    public function testGetWhereNotThere()
    {
        $profile1 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile1Name = $this->getFaker()->unique()->word;
        $profile2 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile2Name = $this->getFaker()->unique()->word;
        $profile3 = new Profile(
            $this->getFaker()->word,
            $this->getFaker()->word
        );
        $profile3Name = $this->getFaker()->unique()->word;

        $this->registry
            ->set($profile1Name, $profile1)
            ->set($profile2Name, $profile2)
            ->set($profile3Name, $profile3)
            ->get($this->getFaker()->unique()->word)
        ;
    }
}
