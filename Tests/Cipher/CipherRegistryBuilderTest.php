<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 4:20 PM
 */

namespace Giftcards\Encryption\Tests\Cipher;

use Giftcards\Encryption\Cipher\CipherRegistry;
use Giftcards\Encryption\Cipher\CipherRegistryBuilder;
use Giftcards\Encryption\Cipher\MysqlAes;
use Giftcards\Encryption\Cipher\NoOp;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class CipherRegistryBuilderTest extends AbstractExtendableTestCase
{
    /** @var  CipherRegistryBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new CipherRegistryBuilder();
    }

    public function testNewInstance()
    {
        $cipherRegistryBuilder = new CipherRegistryBuilder();
        $cipherRegistryBuilder
            ->add(new MysqlAes())
            ->add(new NoOp())
        ;
        $this->assertEquals($cipherRegistryBuilder, CipherRegistryBuilder::newInstance());
    }

    public function testBuild()
    {
        $cipher1 = \Mockery::mock('Giftcards\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn('cipher1')
            ->getMock()
        ;
        $cipher2 = \Mockery::mock('Giftcards\Encryption\Cipher\CipherInterface')
            ->shouldReceive('getName')
            ->andReturn('cipher2')
            ->getMock()
        ;
        $this->builder
            ->add($cipher1)
            ->add($cipher2)
        ;
        $cipherRegistry = new CipherRegistry();
        $cipherRegistry->add($cipher1)->add($cipher2);
        $this->assertEquals($cipherRegistry, $this->builder->build());
    }
}
