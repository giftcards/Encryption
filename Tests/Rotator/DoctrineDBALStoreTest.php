<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 11:01 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use Giftcards\Encryption\CipherText\Rotator\Store\DoctrineDBALStore;
use Giftcards\Encryption\CipherText\Rotator\Record;
use Giftcards\Encryption\Tests\AbstractTestCase;

class DoctrineDBALStoreTest extends AbstractTestCase
{
    public function testFetch()
    {
        $tableName = "TestTable";
        $idColumn = "Id";
        $encryptedColumns = ["TestCol"];
        $allColumns = ["TestCol", "Id"];

        $resultSet = \Mockery::mock(Statement::class);
        $resultSet->shouldReceive("fetchAll")->with(\PDO::FETCH_ASSOC)->andReturn([
            ["Id" => 1, "TestCol" => "encryptedData"],
            ["Id" => 2, "TestCol" => "encryptedData", "OtherCol" => "disregardedData"],
        ]);

        $qb = \Mockery::mock(QueryBuilder::class);
        $qb->shouldReceive("select")->with($allColumns)->andReturnSelf();
        $qb->shouldReceive("from")->withArgs([$tableName, "t"])->andReturnSelf();
        $qb->shouldReceive("setFirstResult")->with(0)->andReturnSelf();
        $qb->shouldReceive("setMaxResults")->with(10)->andReturnSelf();
        $qb->shouldReceive("execute")->withNoArgs()->andReturn($resultSet);

        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive("createQueryBuilder")->andReturn($qb);

        $store = new DoctrineDBALStore($connection, $tableName, $encryptedColumns, $idColumn);
        $records = $store->fetch(0, 10);
        $this->assertEquals([
            new Record(1, ["TestCol" => "encryptedData"]),
            new Record(2, ["TestCol" => "encryptedData"]),
        ], $records);

        $connection->shouldHaveReceived("createQueryBuilder")->withNoArgs();
        $qb->shouldHaveReceived("select")->with($allColumns);
        $qb->shouldHaveReceived("from")->withArgs([$tableName, "t"]);
        $qb->shouldHaveReceived("setFirstResult")->with(0);
        $qb->shouldHaveReceived("setMaxResults")->with(10);
        $qb->shouldHaveReceived("execute")->withNoArgs();

        $resultSet->shouldhaveReceived("fetchAll")->with(\PDO::FETCH_ASSOC);
    }

    public function testSave()
    {
        $tableName = "TestTable";
        $idColumn = "Id";
        $encryptedColumns = ["TestCol"];
        $allColumns = ["TestCol", "Id"];

        /** @var Record[] $records */
        $records = [
            new Record(1, ["TestCol" => "encryptedData"]),
            new Record(2, ["TestCol" => "encryptedData"]),
        ];

        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive("beginTransaction")->withNoArgs();
        $connection->shouldReceive("commit")->withNoArgs();
        $connection->shouldNotReceive("rollBack");
        $connection->shouldReceive("update");

        $store = new \Giftcards\Encryption\CipherText\Rotator\Store\DoctrineDBALStore($connection, $tableName, $encryptedColumns, $idColumn);
        $store->save($records);

        $connection->shouldHaveReceived("beginTransaction")->withNoArgs();
        $connection->shouldHaveReceived("commit")->withNoArgs();
        foreach ($records as $record) {
            $connection->shouldHaveReceived("update")->withArgs([
                $tableName,
                $record->getData(),
                [$idColumn => $record->getId()]
            ]);
        }
    }
}