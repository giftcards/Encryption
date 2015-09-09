<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:33 PM
 */

namespace Giftcards\Encryption\Tests\Cipher;

use Giftcards\Encryption\Cipher\NoOp;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class NoOpTest extends AbstractExtendableTestCase
{
    /** @var  NoOp */
    protected $cipher;

    public function setUp()
    {
        $this->cipher = new NoOp();
    }

    public function testEncipher()
    {
        $string = $this->getFaker()->word;
        $key = $this->getFaker()->word;
        $this->assertEquals($string, $this->cipher->encipher($string, $key));
    }

    public function testDecipher()
    {
        $string = $this->getFaker()->word;
        $key = $this->getFaker()->word;
        $this->assertEquals($string, $this->cipher->decipher($string, $key));
    }

    public function testGetName()
    {
        $this->assertEquals('no_op', $this->cipher->getName());
    }
}
