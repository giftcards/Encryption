<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:23 PM
 */

namespace Giftcards\Encryption\Tests\Key\Factory;

use Guzzle\Http\Client;
use Giftcards\Encryption\Key\Factory\VaultSourceBuilder;
use Giftcards\Encryption\Key\VaultSource;
use Giftcards\Encryption\Vault\AddTokenPlugin;
use Giftcards\Encryption\Vault\TokenAuthTokenSource;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class VaultSourceBuilderTest extends AbstractExtendableTestCase
{
    /** @var  VaultSourceBuilder */
    protected $factory;

    public function setUp() : void
    {
        $this->factory = new VaultSourceBuilder();
    }

    public function testBuild()
    {
        $baseUrl = $this->getFaker()->unique()->url;
        $token = $this->getFaker()->unique()->word;
        $mount = $this->getFaker()->unique()->word;
        $valueField = $this->getFaker()->unique()->word;
        $apiVersion = $this->getFaker()->unique()->word;
        
        $client = new Client(
            $baseUrl,
            ['curl.options' => [CURLOPT_SSLVERSION => 6]]
        );
        $client->addSubscriber(new AddTokenPlugin(new TokenAuthTokenSource($token)));
        $this->assertEquals(new VaultSource(
            $client,
            $mount,
            $valueField,
            $apiVersion
        ), $this->factory->build([
            'base_url' => $baseUrl,
            'token' => $token,
            'mount' => $mount,
            'value_field' => $valueField,
            'api_version' => $apiVersion
        ]));
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(['base_url', 'token'])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with([
                    'mount' => 'secret',
                    'value_field' => 'value',
                    'api_version' => 'v1'
                ])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('base_url', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('token', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('mount', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('value_field', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('api_version', 'string')
                ->andReturnSelf()
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vault', $this->factory->getName());
    }
}
