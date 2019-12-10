<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 10:47 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Faker\Factory;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Giftcards\Encryption\Key\VaultSource;
use Mockery;

class VaultSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $faker = Factory::create();
        $keys = [
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
            $faker->unique()->word => $faker->unique()->word,
        ];
        $missingKeys = [$faker->unique()->word, $faker->unique()->word];

        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        
        $client = Mockery::mock('Guzzle\Http\Client');

        foreach ($keys as $name => $value) {
            $client
                ->shouldReceive('get')
                ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $name))
                ->andReturn(
                    Mockery::mock()
                        ->shouldReceive('send')
                        ->andReturn(
                            Mockery::mock()
                                ->shouldReceive('json')
                                ->andReturn(['data' => [$valueField => $value]])
                                ->getMock()
                        )
                        ->getMock()
                )
            ;
        }

        foreach ($missingKeys as $name) {
            $exception = new ClientErrorResponseException();
            $exception->setResponse(
                Mockery::mock('Guzzle\Http\Message\Response')
                    ->shouldReceive('getStatusCode')
                    ->andReturn(404)
                    ->getMock()
            );
            $client
                ->shouldReceive('get')
                ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $name))
                ->andReturn(
                    Mockery::mock()
                        ->shouldReceive('send')
                        ->andThrow($exception)
                        ->getMock()
                )
            ;
        }

        return [
            [
                new VaultSource(
                    $client,
                    $mount,
                    $valueField,
                    $apiVersion
                ),
                $keys,
                $missingKeys
            ]
        ];
    }

    public function testHasWhereClientExceptionThrownButNot404()
    {
        $this->expectException('\Guzzle\Http\Exception\ClientErrorResponseException');
        $faker = Factory::create();
        $key = $faker->unique()->word;
        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        $exception = new ClientErrorResponseException();
        $exception->setResponse(
            Mockery::mock('Guzzle\Http\Message\Response')
                ->shouldReceive('getStatusCode')
                ->andReturn(400)
                ->getMock()
        );
        $client = Mockery::mock('Guzzle\Http\Client')
            ->shouldReceive('get')
            ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $key))
            ->andReturn(
                Mockery::mock()
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

    public function testGetWhereClientExceptionThrownButNot404()
    {
        $this->expectException('\Guzzle\Http\Exception\ClientErrorResponseException');
        $faker = Factory::create();
        $key = $faker->unique()->word;
        $valueField = $faker->unique()->word;
        $mount = $faker->unique()->word;
        $apiVersion = $faker->unique()->word;
        $exception = new ClientErrorResponseException();
        $exception->setResponse(
            Mockery::mock('Guzzle\Http\Message\Response')
                ->shouldReceive('getStatusCode')
                ->andReturn(400)
                ->getMock()
        );
        $client = Mockery::mock('Guzzle\Http\Client')
            ->shouldReceive('get')
            ->with(sprintf('/%s/%s/%s', $apiVersion, $mount, $key))
            ->andReturn(
                Mockery::mock()
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
