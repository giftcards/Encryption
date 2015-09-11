<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:43 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Giftcards\Encryption\Tests\MockPDO;
use Mockery\MockInterface;
use Giftcards\Encryption\CipherText\Rotator\DatabaseTableRotator;
use Giftcards\Encryption\Tests\AbstractTestCase;

class DatabaseTableRotatorTest extends AbstractTestCase
{
    /** @var  DatabaseTableRotator */
    protected $rotator;
    /** @var  MockInterface */
    protected $pdo;
    protected $table;
    protected $fields;
    protected $idField;

    public function setUp()
    {
        $this->rotator = new DatabaseTableRotator(
            $this->pdo = new MockPDO(\Mockery::mock()),
            $this->table = $this->getFaker()->unique()->word,
            $this->fields = array(
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
            ),
            $this->idField = $this->getFaker()->unique()->word
        );
    }

    public function testRotate()
    {
        $newProfile = $this->getFaker()->word;
        $encryptor = \Mockery::mock('Giftcards\Encryption\Encryptor');
        $observer = \Mockery::mock('Giftcards\Encryption\CipherText\Rotator\ObserverInterface');
        $fields = $this->fields;
        $fields[] = $this->idField;
        $faker = $this->getFaker();
        $row1 = array_combine($fields, array_map(function () use ($faker) {
            return $faker->unique()->word;
        }, $fields));
        $row2 = array_combine($fields, array_map(function () use ($faker) {
            return $faker->unique()->word;
        }, $fields));
        $row3 = array_combine($fields, array_map(function () use ($faker) {
            return $faker->unique()->word;
        }, $fields));
        $this->pdo
            ->shouldReceive('prepare')
            ->once()
            ->with(sprintf(
                'SELECT %s FROM %s',
                implode(', ', $fields),
                $this->table
            ))
            ->andReturn(
                \Mockery::mock()
                    ->shouldReceive('execute')
                    ->once()
                    ->getMock()
                    ->shouldReceive('fetch')
                    ->times(4)
                    ->with(\PDO::FETCH_ASSOC)
                    ->andReturn($row1, $row2, $row3, false)
                    ->getMock()
            )
        ;
        $observer
            ->shouldReceive('rotating')
            ->once()
            ->ordered()
            ->with($row1[$this->idField])
            ->getMock()
            ->shouldReceive('rotated')
            ->once()
            ->ordered()
            ->with($row1[$this->idField])
            ->getMock()
            ->shouldReceive('rotating')
            ->once()
            ->ordered()
            ->with($row2[$this->idField])
            ->getMock()
            ->shouldReceive('rotated')
            ->once()
            ->ordered()
            ->with($row2[$this->idField])
            ->getMock()
            ->shouldReceive('rotating')
            ->once()
            ->ordered()
            ->with($row3[$this->idField])
            ->getMock()
            ->shouldReceive('rotated')
            ->once()
            ->ordered()
            ->with($row3[$this->idField])
            ->getMock()
        ;
        foreach (array($row1, $row2, $row3) as $row) {
            $parameters = array();
            $setFields = array();
            foreach ($row as $field => $value) {
                if ($field == $this->idField) {
                    continue;
                }
                $encryptor
                    ->shouldReceive('decrypt')
                    ->once()
                    ->with($value)
                    ->andReturn($decrypted = $this->getFaker()->unique()->word)
                    ->getMock()
                    ->shouldReceive('encrypt')
                    ->once()
                    ->with($decrypted, $newProfile)
                    ->andReturn($parameters[] = $this->getFaker()->unique()->word)
                    ->getMock()
                ;
                $setFields[] = sprintf('%s = ?', $field);
            }
    
            $parameters[] = $row[$this->idField];
            
            $this->pdo
                ->shouldReceive('prepare')
                ->once()
                ->with(sprintf(
                    'UPDATE %s SET %s WHERE %s = ?',
                    $this->table,
                    implode(',', $setFields),
                    $this->idField
                ))
                ->andReturn(
                    \Mockery::mock()
                        ->shouldReceive('execute')
                        ->once()
                        ->with($parameters)
                        ->getMock()
                )
            ;
        }

        $this->rotator->rotate(
            $observer,
            $encryptor,
            $newProfile
        );
    }
}
