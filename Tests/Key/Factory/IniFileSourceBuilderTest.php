<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\Key\Factory;

use Giftcards\Encryption\Key\Factory\IniFileSourceBuilder;
use Giftcards\Encryption\Key\IniFileSource;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class IniFileSourceBuilderTest extends AbstractExtendableTestCase
{
    /** @var  IniFileSourceBuilder */
    protected $factory;

    public function setUp() : void
    {
        $this->factory = new IniFileSourceBuilder();
    }

    public function testBuild()
    {
        $iniFilePath = $this->getFaker()->unique()->word;
        $caseSensitive = $this->getFaker()->boolean;
        $this->assertEquals(
            new IniFileSource($iniFilePath, $caseSensitive),
            $this->factory->build([
                'file' => $iniFilePath,
                'case_sensitive' => $caseSensitive
            ])
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(['file'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with(['case_sensitive' => false])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('file', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('case_sensitive', 'bool')
                ->andReturnSelf()
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('ini', $this->factory->getName());
    }
}
