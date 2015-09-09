<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 4:54 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator\Factory;

use Giftcards\Encryption\CipherText\Rotator\DatabaseTableRotator;
use Giftcards\Encryption\CipherText\Rotator\Factory\DatabaseTableRotatorBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class DatabaseTableRotatorBuilderTest extends AbstractTestCase
{
    /** @var  DatabaseTableRotatorBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new DatabaseTableRotatorBuilder();
    }

    public function testBuild()
    {
        $pdo = \Mockery::mock('Giftcards\Encryption\Tests\MockPDO');
        $table = $this->getFaker()->unique()->word;
        $fields = array(
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
        );
        $idField = $this->getFaker()->unique()->word;
        $this->assertEquals(
            new DatabaseTableRotator(
                $pdo,
                $table,
                $fields,
                $idField
            ),
            $this->builder->build(array(
                'pdo' => $pdo,
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
                    'pdo',
                    'table',
                    'fields',
                    'id_field'
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with(array(
                    'pdo' => 'PDO',
                    'table' => 'string',
                    'fields' => 'array',
                    'id_field' => 'string'
                ))
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }
}
