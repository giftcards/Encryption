<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 10:42 PM
 */

namespace Giftcards\Encryption\Tests\Vault;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Mockery;
use Mockery\MockInterface;
use Giftcards\Encryption\Vault\AddTokenPlugin;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class AddTokenPluginTest extends AbstractExtendableTestCase
{
    /** @var  AddTokenPlugin */
    protected $plugin;
    /** @var  MockInterface */
    protected $tokenSource;

    public function setUp() : void
    {
        $this->plugin = new AddTokenPlugin(
            $this->tokenSource = Mockery::mock('Giftcards\Encryption\Vault\AuthTokenSourceInterface')
        );
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            ['client.create_request' => ['addAuthToken', 255]],
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
        $this->plugin->addAuthToken(new Event(['request' => $request]));
        $this->assertEquals($token, $request->getHeader('X-Vault-Token'));
    }
}
