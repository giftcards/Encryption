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

use Giftcards\Encryption\Tests\MockPDO;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class DatabaseTableRotatorBuilderTest extends AbstractExtendableTestCase
{
    /** @var  DatabaseTableRotatorBuilder */
    protected $builder;

    public function setUp() : void
    {
        $this->builder = new DatabaseTableRotatorBuilder();
    }

    public function testBuild()
    {
        $pdo = new MockPDO(Mockery::mock());
        $table = $this->getFaker()->unique()->word;
        $fields = [
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
            $this->getFaker()->unique()->word,
        ];
        $idField = $this->getFaker()->unique()->word;
        $this->assertEquals(
            new DatabaseTableRotator(
                $pdo,
                $table,
                $fields,
                $idField
            ),
            $this->builder->build([
                'pdo' => $pdo,
                'table' => $table,
                'fields' => $fields,
                'id_field' => $idField
            ])
        );
    }

    public function testConfigureOptionsResolver()
    {
        $this->builder->configureOptionsResolver(
            Mockery::mock('Symfony\Component\OptionsResolver\OptionsResolver')
                ->shouldReceive('setRequired')
                ->once()
                ->with([
                    'pdo',
                    'table',
                    'fields',
                    'id_field'
                ])
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('pdo', 'PDO')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('table', 'string')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('fields', 'array')
                ->andReturnSelf()
                ->getMock()
                ->shouldReceive('setAllowedTypes')
                ->once()
                ->with('id_field', 'string')
                ->andReturnSelf()
                ->getMock()
        );
    }
}
