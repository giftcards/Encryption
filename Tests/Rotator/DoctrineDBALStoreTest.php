<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 11:01 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Exception;
use Giftcards\Encryption\CipherText\Rotator\Store\DoctrineDBALStore;
use Giftcards\Encryption\CipherText\Rotator\Record;
use Giftcards\Encryption\Tests\AbstractTestCase;

class DoctrineDBALStoreTest extends AbstractTestCase
{
    public function testFetch()
    {
        $tableName = "TestTable";
        $idColumn = "Id";
        $encryptedColumns = array("TestCol");
        $allColumns = array("TestCol", "Id");

        $resultSet = \Mockery::mock("Doctrine\\DBAL\\Driver\\Statement");
        $resultSet->shouldReceive("fetchAll")->with(\PDO::FETCH_ASSOC)->andReturn(array(
            array("Id" => 1, "TestCol" => "encryptedData"),
            array("Id" => 2, "TestCol" => "encryptedData", "OtherCol" => "disregardedData"),
        ));

        $qb = \Mockery::mock("Doctrine\\DBAL\\Query\\QueryBuilder");
        $qb->shouldReceive("select")->with($allColumns)->andReturnSelf();
        $qb->shouldReceive("from")->withArgs(array($tableName, "t"))->andReturnSelf();
        $qb->shouldReceive("setFirstResult")->with(0)->andReturnSelf();
        $qb->shouldReceive("setMaxResults")->with(10)->andReturnSelf();
        $qb->shouldReceive("execute")->withNoArgs()->andReturn($resultSet);

        $connection = \Mockery::mock("Doctrine\\DBAL\\Connection");
        $connection->shouldReceive("createQueryBuilder")->andReturn($qb);

        $store = new DoctrineDBALStore($connection, $tableName, $encryptedColumns, $idColumn);
        $records = $store->fetch(0, 10);
        $this->assertEquals(array(
            new Record(1, array("TestCol" => "encryptedData")),
            new Record(2, array("TestCol" => "encryptedData")),
        ), $records);

        $connection->shouldHaveReceived("createQueryBuilder")->withNoArgs();
        $qb->shouldHaveReceived("select")->with($allColumns);
        $qb->shouldHaveReceived("from")->withArgs(array($tableName, "t"));
        $qb->shouldHaveReceived("setFirstResult")->with(0);
        $qb->shouldHaveReceived("setMaxResults")->with(10);
        $qb->shouldHaveReceived("execute")->withNoArgs();

        $resultSet->shouldhaveReceived("fetchAll")->with(\PDO::FETCH_ASSOC);
    }

    public function testSave()
    {
        $tableName = "TestTable";
        $idColumn = "Id";
        $encryptedColumns = array("TestCol");

        /** @var Record[] $records */
        $records = array(
            new Record(1, array("TestCol" => "encryptedData")),
            new Record(2, array("TestCol" => "encryptedData")),
        );

        $connection = \Mockery::mock("Doctrine\\DBAL\\Connection");
        $connection->shouldReceive("beginTransaction")->withNoArgs();
        $connection->shouldReceive("commit")->withNoArgs();
        $connection->shouldNotReceive("rollBack");
        $connection->shouldReceive("update");

        $store = new DoctrineDBALStore($connection, $tableName, $encryptedColumns, $idColumn);
        $store->save($records);

        $connection->shouldHaveReceived("beginTransaction")->withNoArgs();
        $connection->shouldHaveReceived("commit")->withNoArgs();
        foreach ($records as $record) {
            $connection->shouldHaveReceived("update")->withArgs(array(
                $tableName,
                $record->getData(),
                array($idColumn => $record->getId())
            ));
        }
    }

    public function testException()
    {
        $tableName = "TestTable";
        $idColumn = "Id";
        $encryptedColumns = array("TestCol");

        /** @var Record[] $records */
        $records = array(
            new Record(1, array("TestCol" => "encryptedData")),
            new Record(2, array("TestCol" => "encryptedData")),
        );

        $connection = \Mockery::mock("Doctrine\\DBAL\\Connection");
        $connection->shouldReceive("beginTransaction")->withNoArgs();
        $connection->shouldReceive("update")->andReturnUsing(function () {
            throw new \Exception();
        });
        $connection->shouldNotReceive("commit");
        $connection->shouldReceive("rollBack");

        $store = new DoctrineDBALStore($connection, $tableName, $encryptedColumns, $idColumn);
        try {
            $store->save($records);
        } catch (\Exception $e) {
            $connection->shouldHaveReceived("rollBack");
        }
    }
}
