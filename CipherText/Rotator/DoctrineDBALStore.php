<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 12:52 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Doctrine\DBAL\Connection;
use Exception;

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
    public function __construct(Connection $connection, string $table, array $fields, string $idField)
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
    public function fetch(int $offset, int $limit): array
    {
        $fields = $this->fields;
        $fields[] = $this->idField;
        $stmt = $this->connection->createQueryBuilder()
            ->select($fields)
            ->from($this->table)
            ->setFirstResult($offset)
            ->setMaxResults($offset)
            ->execute();


        return array_map(function ($row) {
            return new Record(
                $row[$this->idField],
                array_intersect_key($row, array_flip($this->fields))
            );
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
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
            foreach ($rotatedRecords as $rotatedRecord) {
                $this->connection->update(
                    $this->table,
                    $rotatedRecord->getData(),
                    array($this->idField => $rotatedRecord->getId())
                );
            }
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}