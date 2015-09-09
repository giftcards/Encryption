<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 4:54 PM
 */

namespace Omni\Encryption\Tests\CipherText\Rotator\Factory;

use Omni\Encryption\CipherText\Rotator\DatabaseTableRotator;
use Omni\Encryption\CipherText\Rotator\Factory\DatabaseTableRotatorBuilder;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class DatabaseTableRotatorBuilderTest extends AbstractExtendableTestCase
{
    /** @var  DatabaseTableRotatorBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new DatabaseTableRotatorBuilder();
    }

    public function testBuild()
    {
        $pdo = $this->getPdoMock(\Mockery::mock());
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
