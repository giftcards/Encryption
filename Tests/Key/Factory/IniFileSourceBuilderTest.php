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
use Giftcards\Encryption\Tests\AbstractTestCase;

class IniFileSourceBuilderTest extends AbstractTestCase
{
    /** @var  IniFileSourceBuilder */
    protected $factory;

    public function setUp()
    {
        $this->factory = new IniFileSourceBuilder();
    }

    public function testBuild()
    {
        $iniFilePath = $this->getFaker()->unique()->word;
        $caseSensitive = $this->getFaker()->boolean;
        $this->assertEquals(
            new IniFileSource($iniFilePath, $caseSensitive),
            $this->factory->build(array(
                'file' => $iniFilePath,
                'case_sensitive' => $caseSensitive
            ))
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array('file'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with(array('case_sensitive' => false))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('file', 'string')
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('case_sensitive', 'bool')
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('ini', $this->factory->getName());
    }
}
