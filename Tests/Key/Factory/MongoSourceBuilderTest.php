<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\Key\Factory;

use Doctrine\MongoDB\Connection;
use Giftcards\Encryption\Key\Factory\MongoSourceBuilder;
use Giftcards\Encryption\Key\MongoSource;
use Giftcards\Encryption\Tests\Mock\Mockery\Matcher\EqualsMatcher;

use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use stdClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MongoSourceBuilderTest extends AbstractExtendableTestCase
{
    /** @var  MongoSourceBuilder */
    protected $factory;

    public function setUp() : void
    {
        $this->factory = new MongoSourceBuilder();
    }

    public function testBuild()
    {
        $connection = Mockery::mock('Doctrine\MongoDB\Connection');
        $database = $this->getFaker()->unique()->word;
        $collection = $this->getFaker()->unique()->word;
        $findByField = $this->getFaker()->unique()->word;
        $valueField = $this->getFaker()->unique()->word;

        $this->assertEquals(new MongoSource(
            $connection,
            $database,
            $collection,
            $findByField,
            $valueField
        ), $this->factory->build([
            'connection' => $connection,
            'database' => $database,
            'collection' => $collection,
            'find_by_field' => $findByField,
            'value_field' => $valueField
        ]));
    }

    public function testConfigureOptionsResolver()
    {
        $resolver = Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
            ->shouldReceive('setRequired')
            ->once()
            ->with(['connection', 'database', 'collection'])
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setDefaults')
            ->once()
            ->with([
                'find_by_field' => 'name',
                'value_field' => 'value'
            ])
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with('connection', ['Doctrine\MongoDB\Connection', 'array'])
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with('database', 'string')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with('collection', 'string')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with('find_by_field', 'string')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with('value_field', 'string')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setNormalizer')
            ->once()
            ->with('connection', new EqualsMatcher(function () {
            }))
            ->andReturnSelf()
            ->getMock()
        ;
        $this->factory->configureOptionsResolver($resolver);
    }

    public function testConnectionNormalizerWithoutArray()
    {
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $connection = Mockery::mock('Doctrine\MongoDB\Connection');

        $options = $resolver->resolve([
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ]);
        $this->assertSame($connection, $options['connection']);
    }

    public function testConnectionNormalizerWithArray()
    {
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $server = $this->getFaker()->unique()->url;
        $connectionOptions = [
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        ];
        $configuration = Mockery::mock('Doctrine\MongoDB\Configuration');
        $eventManager = Mockery::mock('Doctrine\Common\EventManager');
        $connection = [
            'server' => $server,
            'options' => $connectionOptions,
            'configuration' => $configuration,
            'event_manager' => $eventManager
        ];

        $options = $resolver->resolve([
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ]);
        $this->assertEquals(new Connection(
            $server,
            $connectionOptions,
            $configuration,
            $eventManager
        ), $options['connection']);
    }

    public function testConnectionNormalizerWithArrayAndBadOption()
    {
        $this->expectException('\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException');
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $connection = [
            'server' => new stdClass()
        ];

        $resolver->resolve([
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ]);
    }

    public function testGetName()
    {
        $this->assertEquals('mongo', $this->factory->getName());
    }
}
