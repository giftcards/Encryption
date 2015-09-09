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
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MongoSourceFactoryTest extends AbstractTestCase
{
    /** @var  MongoSourceBuilder */
    protected $factory;

    public function setUp()
    {
        $this->factory = new MongoSourceBuilder();
    }

    public function testBuild()
    {
        $connection = \Mockery::mock('Doctrine\MongoDB\Connection');
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
        ), $this->factory->build(array(
            'connection' => $connection,
            'database' => $database,
            'collection' => $collection,
            'find_by_field' => $findByField,
            'value_field' => $valueField
        )));
    }

    public function testConfigureOptionsResolver()
    {
        $resolver = \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
            ->shouldReceive('setRequired')
            ->once()
            ->with(array('connection', 'database', 'collection'))
            ->andReturn(\Mockery::self())
            ->getMock()
            ->shouldReceive('setDefaults')
            ->once()
            ->with(array(
                'find_by_field' => 'name',
                'value_field' => 'value'
            ))
            ->andReturn(\Mockery::self())
            ->getMock()
            ->shouldReceive('setAllowedTypes')
            ->once()
            ->with(array(
                'connection' => array('Doctrine\MongoDB\Connection', 'array'),
                'database' => 'string',
                'collection' => 'string',
                'find_by_field' => 'string',
                'value_field' => 'string'
            ))
            ->andReturn(\Mockery::self())
            ->getMock()
            ->shouldReceive('setNormalizer')
            ->once()
            ->with('connection', new EqualsMatcher(function () {
            }))
            ->andReturn(\Mockery::self())
            ->getMock()
        ;
        $this->factory->configureOptionsResolver($resolver);
    }

    public function testConnectionNormalizerWithoutArray()
    {
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $connection = \Mockery::mock('Doctrine\MongoDB\Connection');

        $options = $resolver->resolve(array(
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ));
        $this->assertSame($connection, $options['connection']);
    }

    public function testConnectionNormalizerWithArray()
    {
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $server = $this->getFaker()->unique()->url;
        $connectionOptions = array(
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word => $this->getFaker()->unique()->word,
        );
        $configuration = \Mockery::mock('Doctrine\MongoDB\Configuration');
        $eventManager = \Mockery::mock('Doctrine\Common\EventManager');
        $connection = array(
            'server' => $server,
            'options' => $connectionOptions,
            'configuration' => $configuration,
            'event_manager' => $eventManager
        );

        $options = $resolver->resolve(array(
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ));
        $this->assertEquals(new Connection(
            $server,
            $connectionOptions,
            $configuration,
            $eventManager
        ), $options['connection']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConnectionNormalizerWithArrayAndBadOption()
    {
        $resolver = new OptionsResolver();
        $this->factory->configureOptionsResolver($resolver);
        $connection = array(
            'server' => new \stdClass()
        );

        $resolver->resolve(array(
            'connection' => $connection,
            'database' => $this->getFaker()->unique()->word,
            'collection' => $this->getFaker()->unique()->word
        ));
    }

    public function testGetName()
    {
        $this->assertEquals('mongo', $this->factory->getName());
    }
}
