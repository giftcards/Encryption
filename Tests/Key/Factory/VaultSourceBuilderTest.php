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
use Giftcards\Encryption\Tests\AbstractTestCase;

class VaultSourceBuilderTest extends AbstractTestCase
{
    /** @var  VaultSourceBuilder */
    protected $factory;

    public function setUp()
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
            array('curl.options' => array(CURLOPT_SSLVERSION => 6))
        );
        $client->addSubscriber(new AddTokenPlugin(new TokenAuthTokenSource($token)));
        $this->assertEquals(new VaultSource(
            $client,
            $mount,
            $valueField,
            $apiVersion
        ), $this->factory->build(array(
            'base_url' => $baseUrl,
            'token' => $token,
            'mount' => $mount,
            'value_field' => $valueField,
            'api_version' => $apiVersion
        )));
    }

    public function testConfigureOptionsResolver()
    {
        $this->factory->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array('base_url', 'token'))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setDefaults')
                ->once()
                ->with(array(
                    'mount' => 'secret',
                    'value_field' => 'value',
                    'api_version' => 'v1'
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array(
                    'base_url' => 'string',
                    'token' => 'string',
                    'mount' => 'string',
                    'value_field' => 'string',
                    'api_version' => 'string',
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vault', $this->factory->getName());
    }
}
