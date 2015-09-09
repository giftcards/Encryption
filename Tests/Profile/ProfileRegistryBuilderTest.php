<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 5:26 PM
 */

namespace Giftcards\Encryption\Tests\Profile;

use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Profile\ProfileRegistry;
use Giftcards\Encryption\Profile\ProfileRegistryBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class ProfileRegistryBuilderTest extends AbstractTestCase
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
