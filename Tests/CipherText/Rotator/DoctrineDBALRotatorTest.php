<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 3:59 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Mockery;
use Mockery\MockInterface;
use Giftcards\Encryption\CipherText\Rotator\DoctrineDBALRotator;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use PDO;

class DoctrineDBALRotatorTest extends AbstractExtendableTestCase
{
    /** @var  DoctrineDBALRotator */
    protected $rotator;
    /** @var  MockInterface */
    protected $connection;
    protected $table;
    protected $fields;
    protected $idField;

    public function setUp() : void
    {
        $this->rotator = new DoctrineDBALRotator(
            $this->connection = Mockery::mock('Doctrine\DBAL\Connection'),
            $this->table = $this->getFaker()->unique()->word,
            $this->fields = [
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
                $this->getFaker()->unique()->word,
            ],
            $this->idField = $this->getFaker()->unique()->word
        );
    }

    public function testRotate()
    {
        $newProfile = $this->getFaker()->word;
        $encryptor = Mockery::mock('Giftcards\Encryption\Encryptor');
        $observer = Mockery::mock('Giftcards\Encryption\CipherText\Rotator\ObserverInterface');
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
        $this->connection
            ->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn(
                Mockery::mock()
                    ->shouldReceive('select')
                    ->once()
                    ->with($fields)
                    ->andReturnSelf()
                    ->getMock()
                    ->shouldReceive('from')
                    ->once()
                    ->with($this->table)
                    ->andReturnSelf()
                    ->getMock()
                    ->shouldReceive('execute')
                    ->once()
                    ->andReturn(
                        Mockery::mock()
                            ->shouldReceive('fetch')
                            ->times(4)
                            ->with(PDO::FETCH_ASSOC)
                            ->andReturn($row1, $row2, $row3, false)
                            ->getMock()
                    )
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
        foreach ([$row1, $row2, $row3] as $row) {
            $encryptedRow = [];
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
                    ->andReturn($encrypted = $this->getFaker()->unique()->word)
                    ->getMock()
                ;
                $encryptedRow[$field] = $encrypted;
            }

            $this->connection
                ->shouldReceive('update')
                ->once()
                ->with(
                    $this->table,
                    $encryptedRow,
                    [$this->idField => $row[$this->idField]]
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
