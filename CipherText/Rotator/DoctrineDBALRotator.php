<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/24/15
 * Time: 6:32 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Doctrine\DBAL\Connection;
use Giftcards\Encryption\Encryptor;

class DoctrineDBALRotator implements RotatorInterface
{
    protected $connection;
    protected $table;
    protected $fields;
    protected $idField;

    /**
     * DoctrineDBALRotator constructor.
     * @param Connection $connection
     * @param $table
     * @param array $fields
     * @param $idField
     */
    public function __construct(Connection $connection, $table, array $fields, $idField)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->fields = $fields;
        $this->idField = $idField;
    }

    public function rotate(
        ObserverInterface $observer,
        Encryptor $encryptor,
        $newProfile = null
    ) {
        $fields = $this->fields;
        $fields[] = $this->idField;
        $stmt = $this->connection->createQueryBuilder()
            ->select($fields)
            ->from($this->table)
            ->execute()
        ;

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row[$this->idField];
            $observer->rotating($id);
            unset($row[$this->idField]);
            foreach ($row as $key => $value) {
                $row[$key] = $encryptor->encrypt($encryptor->decrypt($value), $newProfile);
            }
            $this->connection->update(
                $this->table,
                $row,
                array($this->idField => $id)
            );
            $observer->rotated($id);
        }
    }
}
