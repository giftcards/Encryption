<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 4:54 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator\Factory;

use Giftcards\Encryption\CipherText\Rotator\DatabaseTableRotator;
use Giftcards\Encryption\CipherText\Rotator\DoctrineDBALRotator;
use Giftcards\Encryption\CipherText\Rotator\Factory\DoctrineDBALRotatorBuilder;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class DoctrineDBALRotatorBuilderTest extends AbstractExtendableTestCase
{
    /** @var  DoctrineDBALRotatorBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new DoctrineDBALRotatorBuilder();
    }

    public function testBuild()
    {
        $connection = \Mockery::mock('Doctrine\DBAL\Connection');
        $table = $this->getFaker()->unique()->word;
        $fields = array(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
        );
        $idField = $this->getFaker()->unique()->word;
        $this->assertEquals(
            new DoctrineDBALRotator(
                $connection,
                $table,
                $fields,
                $idField
            ),
            $this->builder->build(array(
                'connection' => $connection,
                'table' => $table,
                'fields' => $fields,
                'id_field' => $idField
            ))
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->builder->configureOptionsResolver(
            \Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with(array(
                    'connection',
                    'table',
                    'fields',
                    'id_field'
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array(
                    'connection' => 'Doctrine\DBAL\Connection',
                    'table' => 'string',
                    'fields' => 'array',
                    'id_field' => 'string'
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }
}
