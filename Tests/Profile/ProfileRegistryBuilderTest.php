<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 5:26 PM
 */

namespace Omni\Encryption\Tests\Profile;

use Omni\Encryption\Profile\Profile;
use Omni\Encryption\Profile\ProfileRegistry;
use Omni\Encryption\Profile\ProfileRegistryBuilder;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ProfileRegistryBuilderTest extends AbstractExtendableTestCase
{
    /** @var  ProfileRegistryBuilder */
    protected $profileRegistryBuilder;

    public function setUp()
    {
        $this->profileRegistryBuilder = new ProfileRegistryBuilder();
    }

    public function testNewInstance()
    {
        $this->assertEquals(new ProfileRegistryBuilder(), ProfileRegistryBuilder::newInstance());
    }

    public function testSet()
    {
        $profile1 = new Profile(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word
        );
        $profile1Name = $this->getFaker()->unique()->word;
        $profile2 = new Profile(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word
        );
        $profile2Name = $this->getFaker()->unique()->word;
        $registry = new ProfileRegistry();
        $registry
            ->set($profile1Name, $profile1)
            ->set($profile2Name, $profile2)
        ;
        $this->profileRegistryBuilder
            ->set($profile1Name, $profile1)
            ->set($profile2Name, $profile2->getCipher(), $profile2->getKeyName())
        ;
        $this->assertEquals($registry, $this->profileRegistryBuilder->build());
    }
}
