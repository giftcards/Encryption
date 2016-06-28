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
use Giftcards\Encryption\Tests\MockPDO;

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
        $pdo = new MockPDO(\Mockery::mock());
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
                ->with('pdo', 'PDO')
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('table', 'string')
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('fields', 'array')
                ->andReturn(\Mockery::self())
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('id_field', 'string')
                ->andReturn(\Mockery::self())
                ->getMock()
        );
    }
}
