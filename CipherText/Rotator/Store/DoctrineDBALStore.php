<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 12:52 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use Doctrine\DBAL\Connection;
use Exception;
use Giftcards\Encryption\CipherText\Rotator\Record;

class DoctrineDBALStore implements StoreInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $table;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $idField;

    /**
     * DoctrineDBALStore constructor.
     * @param Connection $connection
     * @param string $table Table to query from
     * @param array $fields Fields to encrypt
     * @param string $idField Primary key field
     */
    public function __construct(Connection $connection, $table, array $fields, $idField)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->fields = $fields;
        $this->idField = $idField;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function fetch($offset, $limit)
    {
        $fields = $this->fields;
        $fields[] = $this->idField;
        $stmt = $this->connection->createQueryBuilder()
            ->select($fields)
            ->from($this->table, "t")
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->execute();

        return array_map(array($this, "fetchRecord"), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    private function fetchRecord($row)
    {
        return new Record(
            $row[$this->idField],
            array_intersect_key($row, array_flip($this->fields))
        );
    }

    /**
     * @param Record[] $rotatedRecords
     * @return void
     * @throws Exception
     */
    public function save(array $rotatedRecords)
    {
        $this->connection->beginTransaction();
        try {
            array_walk($rotatedRecords, array($this, "saveRecord"));
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    private function saveRecord(Record $rotatedRecord)
    {
        $this->connection->update(
            $this->table,
            $rotatedRecord->getData(),
            array($this->idField => $rotatedRecord->getId())
        );
    }
}