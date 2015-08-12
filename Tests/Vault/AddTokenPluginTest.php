<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 10:42 PM
 */

namespace Omni\Encryption\Tests\Vault;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Mockery\MockInterface;
use Omni\Encryption\Vault\AddTokenPlugin;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class AddTokenPluginTest extends AbstractExtendableTestCase
{
    /** @var  AddTokenPlugin */
    protected $plugin;
    /** @var  MockInterface */
    protected $tokenSource;

    public function setUp()
    {
        $this->plugin = new AddTokenPlugin(
            $this->tokenSource = \Mockery::mock('Omni\Encryption\Vault\AuthTokenSourceInterface')
        );
    }

    public function testGetSUbscribedEvents()
    {
        $this->assertEquals(
            array('client.create_request' => array('addAuthToken', 255)),
            $this->plugin->getSubscribedEvents()
        );
    }

    public function testAddAuthToken()
    {
        $token = $this->getFaker()->word;
        $request = new Request('', '');
        $this->tokenSource
            ->shouldReceive('getToken')
            ->once()
            ->with($request)
            ->andReturn($token)
        ;
        $this->plugin->addAuthToken(new Event(array('request' => $request)));
        $this->assertEquals($token, $request->getHeader('X-Vault-Token', $token));
    }
}
