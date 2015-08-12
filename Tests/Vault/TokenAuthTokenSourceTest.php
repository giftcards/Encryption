<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 10:36 PM
 */

namespace Omni\Encryption\Tests\Vault;

use Guzzle\Http\Message\Request;
use Omni\Encryption\Vault\TokenAuthTokenSource;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class TokenAuthTokenSourceTest extends AbstractExtendableTestCase
{
    /** @var  TokenAuthTokenSource */
    protected $source;
    protected $token;

    public function setUp()
    {
        $this->source = new TokenAuthTokenSource(
            $this->token = $this->getFaker()->word
        );
    }

    public function testGetToken()
    {
        $this->assertEquals($this->token, $this->source->getToken(new Request('', '')));
    }
}
