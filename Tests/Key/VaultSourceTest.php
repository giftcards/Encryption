<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 10:47 PM
 */

namespace Omni\Encryption\Tests\Key;

use Faker\Factory;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Omni\Encryption\Key\VaultSource;

class VaultSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = array(
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        );
        $missingKeys = array($faker->unique()->word, $faker->unique()->word);

        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        
        $client = \Mockery::mock('Guzzle\Http\Client');

        foreach ($keys as $name => $value) {
            $client
                ->shouldReceive('get')
                ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $name))
                ->andReturn(
                    \Mockery::mock()
                        ->shouldReceive('send')
                        ->andReturn(
                            \Mockery::mock()
                                ->shouldReceive('json')
                                ->andReturn(array('data' => array($valueField => $value)))
                                ->getMock()
                        )
                        ->getMock()
                )
            ;
        }

        foreach ($missingKeys as $name) {
            $exception = new ClientErrorResponseException();
            $exception->setResponse(
                \Mockery::mock('Guzzle\Http\Message\Response')
                    ->shouldReceive('getStatusCode')
                    ->andReturn(404)
                    ->getMock()
            );
            $client
                ->shouldReceive('get')
                ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $name))
                ->andReturn(
                    \Mockery::mock()
                        ->shouldReceive('send')
                        ->andThrow($exception)
                        ->getMock()
                )
            ;
        }

        return array(
            array(
                new VaultSource(
                    $client,
                    $mount,
                    $valueField,
                    $apiVersion
                ),
                $keys,
                $missingKeys
            )
        );
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testHasWhereClientExceptionThrownButNot404()
    {
        $faker = Factory::create();
        $key = $faker->unique()->word;
        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        $exception = new ClientErrorResponseException();
        $exception->setResponse(
            \Mockery::mock('Guzzle\Http\Message\Response')
                ->shouldReceive('getStatusCode')
                ->andReturn(400)
                ->getMock()
        );
        $client = \Mockery::mock('Guzzle\Http\Client')
            ->shouldReceive('get')
            ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $key))
            ->andReturn(
                \Mockery::mock()
                    ->shouldReceive('send')
                    ->andThrow($exception)
                    ->getMock()
            )
            ->getMock()
        ;
        $source = new VaultSource(
            $client,
            $mount,
            $valueField,
            $apiVersion
        );
        $source->has($key);
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testGetWhereClientExceptionThrownButNot404()
    {
        $faker = Factory::create();
        $key = $faker->unique()->word;
        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        $exception = new ClientErrorResponseException();
        $exception->setResponse(
            \Mockery::mock('Guzzle\Http\Message\Response')
                ->shouldReceive('getStatusCode')
                ->andReturn(400)
                ->getMock()
        );
        $client = \Mockery::mock('Guzzle\Http\Client')
            ->shouldReceive('get')
            ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $key))
            ->andReturn(
                \Mockery::mock()
                    ->shouldReceive('send')
                    ->andThrow($exception)
                    ->getMock()
            )
            ->getMock()
        ;
        $source = new VaultSource(
            $client,
            $mount,
            $valueField,
            $apiVersion
        );
        $source->get($key);
    }
}
